
<?php

class TimeModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Busca a sala vinculada ao representante
    public function buscarSalaRepresentante(int $idUsuario)
    {
        $sql = "SELECT id_sala
                FROM usuario
                WHERE id = :id_usuario
                AND tipo = 'representante'
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $idUsuario
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lista modalidades que a sala ainda não cadastrou
    public function listarModalidadesDisponiveis(int $idSala)
    {
        $sql = "SELECT m.id, m.nome, m.max_jogadores AS quantidade, m.genero
                FROM modalidade m
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM `time` t
                    INNER JOIN time_jogador tj ON tj.id_time = t.id
                    WHERE t.id_modalidade = m.id
                    AND t.id_sala = :id_sala
                )
                ORDER BY m.nome, m.genero";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_sala' => $idSala
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lista alunos da sala com gênero compatível

    public function listarAlunosDisponiveis(int $idSala, int $idModalidade)
    {
        $sql = "SELECT u.id, u.nome, u.rm, u.genero, u.tipo
            FROM usuario u
            INNER JOIN modalidade m
                ON m.id = :id_modalidade
            WHERE u.id_sala = :id_sala
            AND u.tipo IN ('aluno', 'representante')
            AND (
                m.genero = 'Misto'
                OR (LOWER(m.genero) IN ('masculino', 'masc') AND LOWER(u.genero) = 'masculino')
                OR (LOWER(m.genero) IN ('feminino', 'fem') AND LOWER(u.genero) = 'feminino')
            )
            AND NOT EXISTS (
                SELECT 1
                FROM time_jogador ta
                INNER JOIN `time` t
                    ON t.id = ta.id_time
                WHERE ta.id_usuario = u.id
                AND t.id_modalidade = m.id
            )
            ORDER BY u.nome";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id_sala' => $idSala,
            ':id_modalidade' => $idModalidade
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cadastra o time e seus participantes
    public function cadastrarTime(
        int $idSala,
        int $idModalidade,
        array $alunos
    ) {
        try {
            $this->pdo->beginTransaction();

            // Confere se a modalidade ainda está disponível
            $sql = "SELECT id, max_jogadores AS quantidade, genero
                    FROM modalidade
                    WHERE id = :id_modalidade
                    FOR UPDATE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_modalidade' => $idModalidade
            ]);

            $modalidade = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$modalidade) {
                throw new Exception("Modalidade não encontrada.");
            }

            // Confere a quantidade exata de alunos
            $alunos = array_map('intval', $alunos);
            $alunos = array_values(array_unique($alunos));

            if (count($alunos) !== (int) $modalidade['quantidade']) {
                throw new Exception(
                    "O time deve possuir exatamente {$modalidade['quantidade']} alunos."
                );
            }

            // Verifica se a sala já possui essa modalidade
            $sql = "SELECT id
                    FROM `time`
                    WHERE id_sala = :id_sala
                    AND id_modalidade = :id_modalidade";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_sala' => $idSala,
                ':id_modalidade' => $idModalidade
            ]);

            $idTime = $stmt->fetchColumn();
            if ($idTime !== false) {
                $stmt = $this->pdo->prepare(
                    "SELECT COUNT(*) FROM time_jogador WHERE id_time = :id_time"
                );
                $stmt->execute([':id_time' => $idTime]);
                if ((int) $stmt->fetchColumn() > 0) {
                    throw new Exception(
                        "Sua sala já possui um time cadastrado nessa modalidade."
                    );
                }
            }

            // Valida todos os alunos no servidor
            $placeholders = implode(',', array_fill(0, count($alunos), '?'));

            $sql = "SELECT u.id
        FROM usuario u
        WHERE u.id_sala = ?
        AND u.tipo IN ('aluno', 'representante')
        AND (
            LOWER(TRIM(?)) = 'misto'
            OR (
                LOWER(TRIM(?)) IN ('masculino', 'masc')
                AND LOWER(TRIM(u.genero)) = 'masculino'
            )
            OR (
                LOWER(TRIM(?)) IN ('feminino', 'fem')
                AND LOWER(TRIM(u.genero)) = 'feminino'
            )
        )
        AND u.id IN ($placeholders)
        AND NOT EXISTS (
            SELECT 1
            FROM time_jogador ta
            INNER JOIN `time` t
                ON t.id = ta.id_time
            WHERE ta.id_usuario = u.id
            AND t.id_modalidade = ?
        )";

            $stmt = $this->pdo->prepare($sql);

            $parametros = array_merge(
                [
                    $idSala,
                    $modalidade['genero'],
                    $modalidade['genero'],
                    $modalidade['genero']
                ],
                $alunos,
                [$idModalidade]
            );

            $stmt->execute($parametros);

            $alunosValidos = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (count($alunosValidos) !== count($alunos)) {
                throw new Exception(
                    "Um ou mais alunos são inválidos, não pertencem à sua sala, possuem gênero incompatível ou já participam de outro time dessa modalidade."
                );
            }

            // Insere o time
            if ($idTime === false) {
                $sql = "INSERT INTO `time` (id_modalidade, id_sala)
                        VALUES (:id_modalidade, :id_sala)";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':id_modalidade' => $idModalidade,
                    ':id_sala' => $idSala
                ]);
                $idTime = (int) $this->pdo->lastInsertId();
            } else {
                $idTime = (int) $idTime;
            }

            // Insere os participantes
            $sql = "INSERT INTO time_jogador (id_time, id_usuario)
                    VALUES (:id_time, :id_usuario)";

            $stmt = $this->pdo->prepare($sql);

            foreach ($alunos as $idAluno) {
                $stmt->execute([
                    ':id_time' => $idTime,
                    ':id_usuario' => $idAluno
                ]);
            }

            $this->pdo->commit();

            return true;
        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
