
<?php

class JogoModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Lista as salas cadastradas
    public function listarSalas(): array
    {
        $sql = '
            SELECT id, nome
            FROM sala
            ORDER BY nome
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lista as modalidades
    public function listarModalidades(): array
    {
        $sql = '
            SELECT id, nome, genero
            FROM modalidade
            ORDER BY nome, genero
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica se as duas salas existem e são diferentes
    private function validarSalas($idSalaA, $idSalaB): bool
    {
        if ((int) $idSalaA === (int) $idSalaB) {
            return false;
        }

        $sql = '
            SELECT COUNT(*)
            FROM sala
            WHERE id IN (:sala_a, :sala_b)
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'sala_a' => $idSalaA,
            'sala_b' => $idSalaB
        ]);

        return (int) $stmt->fetchColumn() === 2;
    }

    // Verifica se a modalidade existe
    private function validarModalidade($idModalidade): bool
    {
        $sql = '
            SELECT id
            FROM modalidade
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $idModalidade
        ]);

        return (bool) $stmt->fetchColumn();
    }

    // Cadastra um confronto
    public function cadastrar(
        $idModalidade,
        $idSalaA,
        $idSalaB,
        string $dataHora
    ): bool {
        try {
            if (!$this->validarSalas($idSalaA, $idSalaB)) {
                return false;
            }

            if (!$this->validarModalidade($idModalidade)) {
                return false;
            }

            $sql = '
                INSERT INTO jogo (
                    id_modalidade,
                    id_salaA,
                    id_salaB,
                    dia,
                    ganhador,
                    quadra
                ) VALUES (
                    :modalidade,
                    :sala_a,
                    :sala_b,
                    :dia,
                    NULL,
                    :quadra
                )
            ';

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                'modalidade' => $idModalidade,
                'sala_a' => $idSalaA,
                'sala_b' => $idSalaB,
                'dia' => $dataHora,
                'quadra' => ''
            ]);

        } catch (PDOException $e) {
            error_log('Erro ao cadastrar confronto: ' . $e->getMessage());

            return false;
        }
    }

    // Lista os confrontos cadastrados
    public function listar(): array
    {
        $sql = '
            SELECT
                j.id,
                DATE_FORMAT(j.dia, "%d/%m/%Y") AS dia,
                TIME_FORMAT(j.dia, "%H:%i") AS horario,
                j.ganhador,
                j.quadra,
                m.nome AS modalidade,
                m.genero AS genero,
                salaA.nome AS sala_a,
                salaB.nome AS sala_b

            FROM jogo j

            INNER JOIN modalidade m
                ON m.id = j.id_modalidade

            INNER JOIN sala salaA
                ON salaA.id = j.id_salaA

            INNER JOIN sala salaB
                ON salaB.id = j.id_salaB

            ORDER BY j.dia, j.id
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
