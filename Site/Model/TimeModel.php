
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
        $sql = "SELECT m.id, m.nome, m.quantidade, m.genero
                FROM modalidade m
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM `time` t
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
                OR (m.genero = 'Masc' AND u.genero = 'masculino')
                OR (m.genero = 'Fem' AND u.genero = 'feminino')
            )
            AND NOT EXISTS (
                SELECT 1
                FROM time_aluno ta
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
            $sql = "SELECT id, quantidade, genero
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

            if ($stmt->fetch()) {
                throw new Exception(
                    "Sua sala já possui um time nessa modalidade."
                );
            }

            // Valida todos os alunos no servidor
            $placeholders = implode(',', array_fill(0, count($alunos), '?'));

          $sql = "SELECT u.id
        FROM usuario u
        WHERE u.id_sala = ?
        AND u.tipo IN ('aluno', 'representante')
        AND (
            ? = 'Misto'
            OR (? = 'Masc' AND u.genero = 'masculino')
            OR (? = 'Fem' AND u.genero = 'feminino')
        )
        AND u.id IN ($placeholders)
        AND NOT EXISTS (
            SELECT 1
            FROM time_aluno ta
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
            $sql = "INSERT INTO `time` (id_modalidade, id_sala)
                    VALUES (:id_modalidade, :id_sala)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_modalidade' => $idModalidade,
                ':id_sala' => $idSala
            ]);

            $idTime = (int) $this->pdo->lastInsertId();

            // Insere os participantes
            $sql = "INSERT INTO time_aluno (id_time, id_usuario)
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
