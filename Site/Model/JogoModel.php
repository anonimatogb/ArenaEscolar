<?php
require_once dirname(__DIR__, 2) . '/DB/database.php';

class JogoModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarSalas(): array
    {
        $stmt = $this->pdo->query('SELECT id, nome, divisao FROM sala ORDER BY divisao, nome');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarModalidades(): array
    {
        $stmt = $this->pdo->query('SELECT id, nome FROM modalidade ORDER BY nome');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrar($idModalidade, $idSalaA, $idSalaB, $dia, $horario): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, divisao FROM sala WHERE id IN (:sala_a, :sala_b)'
            );
            $stmt->execute(['sala_a' => $idSalaA, 'sala_b' => $idSalaB]);
            $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (
                count($salas) !== 2 ||
                $idSalaA === $idSalaB ||
                (int) $salas[0]['divisao'] !== (int) $salas[1]['divisao']
            ) {
                return false;
            }

            $stmt = $this->pdo->prepare('SELECT 1 FROM modalidade WHERE id = :id');
            $stmt->execute(['id' => $idModalidade]);
            if (!$stmt->fetchColumn()) {
                return false;
            }

            $stmt = $this->pdo->prepare(
                'INSERT INTO jogo
                    (id_modalidade, id_salaA, id_salaB, dia, horario,
                     id_timeA, id_timeB, ganhador, quadra)
                 VALUES
                    (:modalidade, :sala_a, :sala_b, :dia, :horario,
                     NULL, NULL, :ganhador, :quadra)'
            );

            return $stmt->execute([
                'modalidade' => $idModalidade,
                'sala_a' => $idSalaA,
                'sala_b' => $idSalaB,
                'dia' => $dia,
                'horario' => $horario,
                'ganhador' => '',
                'quadra' => '',
            ]);
        } catch (PDOException $e) {
            error_log('Erro ao cadastrar confronto: ' . $e->getMessage());
            return false;
        }
    }

    public function listar(): array
    {
        $stmt = $this->pdo->query(
            'SELECT j.id, j.dia, j.horario, m.nome AS modalidade,
                    salaA.nome AS sala_a, salaB.nome AS sala_b
             FROM jogo j
             INNER JOIN modalidade m ON m.id = j.id_modalidade
             INNER JOIN sala salaA ON salaA.id = j.id_salaA
             INNER JOIN sala salaB ON salaB.id = j.id_salaB
             ORDER BY j.dia, j.horario, j.id'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
