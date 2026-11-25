
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

    public function listarModalidadesParaCadastroAluno(int $idSala): array
    {
        $sql = "SELECT m.id, m.nome, m.genero, m.max_jogadores AS quantidade,
                       COUNT(tj.id) AS participantes
                FROM modalidade m
                LEFT JOIN `time` t
                    ON t.id_modalidade = m.id
                    AND t.id_sala = :id_sala
                LEFT JOIN time_jogador tj
                    ON tj.id_time = t.id
                GROUP BY m.id, m.nome, m.genero, m.max_jogadores
                HAVING COUNT(tj.id) < m.max_jogadores
                ORDER BY m.nome, m.genero";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_sala' => $idSala]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTimesPorSala(int $idSala): array
    {
        $sql = "SELECT m.id AS id_modalidade, m.nome AS modalidade, m.genero,
                       m.max_jogadores AS quantidade, t.id AS id_time,
                       u.id AS id_aluno, u.nome AS aluno, u.rm, u.genero AS genero_aluno
                FROM `time` t
                INNER JOIN modalidade m
                    ON m.id = t.id_modalidade
                LEFT JOIN time_jogador tj
                    ON tj.id_time = t.id
                LEFT JOIN usuario u
                    ON u.id = tj.id_usuario
                WHERE t.id_sala = :id_sala
                ORDER BY m.nome, m.genero, u.nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_sala' => $idSala]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarModalidadesParaEditarAluno(
        int $idSala,
        int $idAluno
    ): array {
        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM usuario
             WHERE id = :id_aluno
               AND id_sala = :id_sala
               AND tipo = 'aluno'"
        );
        $stmt->execute([
            ':id_aluno' => $idAluno,
            ':id_sala' => $idSala,
        ]);

        if ($stmt->fetchColumn() === false) {
            throw new DomainException('Esse aluno não pertence à sua sala.');
        }

        $sql = "SELECT m.id, m.nome, m.genero, m.max_jogadores AS quantidade,
                       COUNT(tj.id) AS participantes,
                       MAX(CASE WHEN tj.id_usuario = :id_aluno THEN 1 ELSE 0 END) AS selecionada
                FROM modalidade m
                LEFT JOIN `time` t
                    ON t.id_modalidade = m.id
                    AND t.id_sala = :id_sala
                LEFT JOIN time_jogador tj
                    ON tj.id_time = t.id
                GROUP BY m.id, m.nome, m.genero, m.max_jogadores
                ORDER BY m.nome, m.genero";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_aluno' => $idAluno,
            ':id_sala' => $idSala,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrarAlunoNaModalidade(
        int $idSala,
        int $idModalidade,
        string $nome,
        string $rm,
        string $genero
    ): int {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, max_jogadores AS quantidade, genero
                 FROM modalidade
                 WHERE id = :id_modalidade
                 FOR UPDATE'
            );
            $stmt->execute([':id_modalidade' => $idModalidade]);
            $modalidade = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$modalidade) {
                throw new DomainException('Modalidade não encontrada.');
            }

            if (!$this->generoCompativel($modalidade['genero'], $genero)) {
                throw new DomainException(
                    'O gênero do aluno não é compatível com essa modalidade.'
                );
            }

            $stmt = $this->pdo->prepare(
                'SELECT id
                 FROM `time`
                 WHERE id_sala = :id_sala
                   AND id_modalidade = :id_modalidade
                 FOR UPDATE'
            );
            $stmt->execute([
                ':id_sala' => $idSala,
                ':id_modalidade' => $idModalidade,
            ]);
            $idTime = $stmt->fetchColumn();

            if ($idTime === false) {
                $stmt = $this->pdo->prepare(
                    'INSERT INTO `time` (id_sala, id_modalidade)
                     VALUES (:id_sala, :id_modalidade)'
                );
                $stmt->execute([
                    ':id_sala' => $idSala,
                    ':id_modalidade' => $idModalidade,
                ]);
                $idTime = (int) $this->pdo->lastInsertId();
            } else {
                $idTime = (int) $idTime;
            }

            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*)
                 FROM time_jogador
                 WHERE id_time = :id_time'
            );
            $stmt->execute([':id_time' => $idTime]);

            if ((int) $stmt->fetchColumn() >= (int) $modalidade['quantidade']) {
                throw new DomainException(
                    'O time dessa modalidade já atingiu o limite de participantes.'
                );
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO usuario (nome, rm, senha, tipo, id_sala, genero)
                 VALUES (:nome, :rm, NULL, 'aluno', :id_sala, :genero)"
            );
            $stmt->execute([
                ':nome' => trim($nome),
                ':rm' => trim($rm),
                ':id_sala' => $idSala,
                ':genero' => trim($genero),
            ]);
            $idAluno = (int) $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare(
                'INSERT INTO time_jogador (id_time, id_usuario)
                 VALUES (:id_time, :id_usuario)'
            );
            $stmt->execute([
                ':id_time' => $idTime,
                ':id_usuario' => $idAluno,
            ]);

            $this->pdo->commit();

            return $idAluno;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function atualizarAlunoEModalidades(
        int $idSala,
        int $idAluno,
        string $nome,
        string $rm,
        string $genero,
        array $idModalidades
    ): void {
        $idModalidades = array_values(array_unique(array_map('intval', $idModalidades)));

        if (count($idModalidades) !== count(array_filter(
            $idModalidades,
            static function (int $idModalidade): bool {
                return $idModalidade > 0;
            }
        ))) {
            throw new DomainException('Selecione modalidades válidas.');
        }

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "SELECT id
                 FROM usuario
                 WHERE id = :id_aluno
                   AND id_sala = :id_sala
                   AND tipo = 'aluno'
                 FOR UPDATE"
            );
            $stmt->execute([
                ':id_aluno' => $idAluno,
                ':id_sala' => $idSala,
            ]);

            if ($stmt->fetchColumn() === false) {
                throw new DomainException('Esse aluno não pertence à sua sala.');
            }

            $stmt = $this->pdo->prepare(
                'SELECT DISTINCT t.id_modalidade
                 FROM time_jogador tj
                 INNER JOIN `time` t
                    ON t.id = tj.id_time
                 WHERE tj.id_usuario = :id_aluno
                   AND t.id_sala = :id_sala'
            );
            $stmt->execute([
                ':id_aluno' => $idAluno,
                ':id_sala' => $idSala,
            ]);
            $modalidadesAtuais = array_map(
                'intval',
                $stmt->fetchAll(PDO::FETCH_COLUMN)
            );

            $idsParaBloquear = array_values(array_unique(array_merge(
                $modalidadesAtuais,
                $idModalidades
            )));
            sort($idsParaBloquear, SORT_NUMERIC);

            $dadosModalidades = [];
            if ($idsParaBloquear !== []) {
                $placeholders = implode(
                    ',',
                    array_fill(0, count($idsParaBloquear), '?')
                );
                $stmt = $this->pdo->prepare(
                    "SELECT id, genero, max_jogadores AS quantidade
                     FROM modalidade
                     WHERE id IN ($placeholders)
                     ORDER BY id
                     FOR UPDATE"
                );
                $stmt->execute($idsParaBloquear);

                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $modalidade) {
                    $dadosModalidades[(int) $modalidade['id']] = $modalidade;
                }
            }

            foreach ($idModalidades as $idModalidade) {
                if (!isset($dadosModalidades[$idModalidade])) {
                    throw new DomainException('Uma das modalidades selecionadas não existe.');
                }

                if (!$this->generoCompativel(
                    $dadosModalidades[$idModalidade]['genero'],
                    $genero
                )) {
                    throw new DomainException(
                        'O gênero do aluno não é compatível com uma das modalidades selecionadas.'
                    );
                }
            }

            $modalidadesNovas = array_values(array_diff(
                $idModalidades,
                $modalidadesAtuais
            ));

            foreach ($modalidadesNovas as $idModalidade) {
                $stmt = $this->pdo->prepare(
                    'SELECT id
                     FROM `time`
                     WHERE id_sala = :id_sala
                       AND id_modalidade = :id_modalidade
                     FOR UPDATE'
                );
                $stmt->execute([
                    ':id_sala' => $idSala,
                    ':id_modalidade' => $idModalidade,
                ]);
                $idTime = $stmt->fetchColumn();

                if ($idTime === false) {
                    $stmt = $this->pdo->prepare(
                        'INSERT INTO `time` (id_sala, id_modalidade)
                         VALUES (:id_sala, :id_modalidade)'
                    );
                    $stmt->execute([
                        ':id_sala' => $idSala,
                        ':id_modalidade' => $idModalidade,
                    ]);
                    $idTime = (int) $this->pdo->lastInsertId();
                } else {
                    $idTime = (int) $idTime;
                }

                $stmt = $this->pdo->prepare(
                    'SELECT COUNT(*)
                     FROM time_jogador
                     WHERE id_time = :id_time'
                );
                $stmt->execute([':id_time' => $idTime]);

                if ((int) $stmt->fetchColumn() >= (int) $dadosModalidades[$idModalidade]['quantidade']) {
                    throw new DomainException(
                        'Não há vagas disponíveis em uma das modalidades selecionadas.'
                    );
                }
            }

            $stmt = $this->pdo->prepare(
                "UPDATE usuario
                 SET nome = :nome, rm = :rm, genero = :genero
                 WHERE id = :id_aluno
                   AND id_sala = :id_sala
                   AND tipo = 'aluno'"
            );
            $stmt->execute([
                ':nome' => trim($nome),
                ':rm' => trim($rm),
                ':genero' => trim($genero),
                ':id_aluno' => $idAluno,
                ':id_sala' => $idSala,
            ]);

            if ($idModalidades === []) {
                $stmt = $this->pdo->prepare(
                    'DELETE tj
                     FROM time_jogador tj
                     INNER JOIN `time` t
                        ON t.id = tj.id_time
                     WHERE tj.id_usuario = :id_aluno
                       AND t.id_sala = :id_sala'
                );
                $stmt->execute([
                    ':id_aluno' => $idAluno,
                    ':id_sala' => $idSala,
                ]);
            } else {
                $placeholders = implode(',', array_fill(0, count($idModalidades), '?'));
                $stmt = $this->pdo->prepare(
                    "DELETE tj
                     FROM time_jogador tj
                     INNER JOIN `time` t
                        ON t.id = tj.id_time
                     WHERE tj.id_usuario = ?
                       AND t.id_sala = ?
                       AND t.id_modalidade NOT IN ($placeholders)"
                );
                $stmt->execute(array_merge(
                    [$idAluno, $idSala],
                    $idModalidades
                ));
            }

            foreach ($modalidadesNovas as $idModalidade) {
                $stmt = $this->pdo->prepare(
                    'SELECT id
                     FROM `time`
                     WHERE id_sala = :id_sala
                       AND id_modalidade = :id_modalidade'
                );
                $stmt->execute([
                    ':id_sala' => $idSala,
                    ':id_modalidade' => $idModalidade,
                ]);
                $idTime = (int) $stmt->fetchColumn();

                $stmt = $this->pdo->prepare(
                    'INSERT INTO time_jogador (id_time, id_usuario)
                     VALUES (:id_time, :id_usuario)'
                );
                $stmt->execute([
                    ':id_time' => $idTime,
                    ':id_usuario' => $idAluno,
                ]);
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
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

    private function generoCompativel(string $generoModalidade, string $generoAluno): bool
    {
        $generoModalidade = strtolower(trim($generoModalidade));
        $generoAluno = strtolower(trim($generoAluno));

        if ($generoModalidade === 'misto') {
            return in_array($generoAluno, ['masculino', 'feminino'], true);
        }

        if ($generoModalidade === 'masc') {
            $generoModalidade = 'masculino';
        } elseif ($generoModalidade === 'fem') {
            $generoModalidade = 'feminino';
        }

        return $generoModalidade === $generoAluno
            && in_array($generoAluno, ['masculino', 'feminino'], true);
    }
}
