<?php

class JogoModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarSalas(): array
    {
        return $this->pdo->query(
            "SELECT id, nome FROM sala WHERE nome <> 'Diretoria' ORDER BY nome"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarModalidades(): array
    {
        return $this->pdo->query(
            'SELECT id, nome, genero, max_jogadores
             FROM modalidade
             ORDER BY nome, genero'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrar(
        int $idModalidade,
        int $idSalaA,
        int $idSalaB,
        string $dia,
        string $horario,
        string $quadra
    ): bool {
        if ($idSalaA === $idSalaB) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            $modalidade = $this->pdo->prepare(
                'SELECT id FROM modalidade WHERE id = :id FOR UPDATE'
            );
            $modalidade->execute(['id' => $idModalidade]);
            if ($modalidade->fetchColumn() === false) {
                throw new DomainException('Modalidade não encontrada.');
            }

            $salas = $this->pdo->prepare(
                "SELECT id FROM sala
                 WHERE id IN (:sala_a, :sala_b) AND nome <> 'Diretoria'
                 FOR UPDATE"
            );
            $salas->execute(['sala_a' => $idSalaA, 'sala_b' => $idSalaB]);
            if (count($salas->fetchAll(PDO::FETCH_COLUMN)) !== 2) {
                throw new DomainException('Selecione duas salas válidas.');
            }

            $inserirTime = $this->pdo->prepare(
                'INSERT IGNORE INTO `time` (id_sala, id_modalidade)
                 VALUES (:id_sala, :id_modalidade)'
            );
            $buscarTime = $this->pdo->prepare(
                'SELECT id FROM `time`
                 WHERE id_sala = :id_sala AND id_modalidade = :id_modalidade'
            );
            $times = [];

            foreach ([$idSalaA, $idSalaB] as $idSala) {
                $inserirTime->execute([
                    'id_sala' => $idSala,
                    'id_modalidade' => $idModalidade,
                ]);
                $buscarTime->execute([
                    'id_sala' => $idSala,
                    'id_modalidade' => $idModalidade,
                ]);
                $idTime = $buscarTime->fetchColumn();
                if ($idTime === false) {
                    throw new RuntimeException('Não foi possível criar o time da sala.');
                }
                $times[] = (int) $idTime;
            }

            $jogo = $this->pdo->prepare(
                "INSERT INTO jogo
                    (id_modalidade, id_timeA, id_timeB, dia, horario, quadra)
                 VALUES
                    (:modalidade, :time_a, :time_b, :dia, :horario, :quadra)"
            );
            $jogo->execute([
                'modalidade' => $idModalidade,
                'time_a' => $times[0],
                'time_b' => $times[1],
                'dia' => $dia,
                'horario' => $horario,
                'quadra' => $quadra,
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function registrarPlacar(int $idJogo, int $pontosA, int $pontosB): void
    {
        if ($pontosA < 0 || $pontosB < 0) {
            throw new DomainException('O placar não pode ser negativo.');
        }

        $this->pdo->beginTransaction();

        try {
            $consulta = $this->pdo->prepare(
                'SELECT j.id_timeA, j.id_timeB,
                        m.pts_vitoria, m.pts_empate, m.pts_derrota
                 FROM jogo j
                 INNER JOIN modalidade m ON m.id = j.id_modalidade
                 WHERE j.id = :id
                 FOR UPDATE'
            );
            $consulta->execute(['id' => $idJogo]);
            $jogo = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$jogo) {
                throw new DomainException('Jogo não encontrado.');
            }

            if ($pontosA > $pontosB) {
                $vencedor = (int) $jogo['id_timeA'];
                $pontosSalaA = (int) $jogo['pts_vitoria'];
                $pontosSalaB = (int) $jogo['pts_derrota'];
            } elseif ($pontosB > $pontosA) {
                $vencedor = (int) $jogo['id_timeB'];
                $pontosSalaA = (int) $jogo['pts_derrota'];
                $pontosSalaB = (int) $jogo['pts_vitoria'];
            } else {
                $vencedor = null;
                $pontosSalaA = (int) $jogo['pts_empate'];
                $pontosSalaB = (int) $jogo['pts_empate'];
            }

            $placar = $this->pdo->prepare(
                'INSERT INTO placar
                    (id_jogo, gols_pts_a, gols_pts_b, pontos_sala_a, pontos_sala_b)
                 VALUES
                    (:id_jogo, :gols_a, :gols_b, :pontos_a, :pontos_b)
                 ON DUPLICATE KEY UPDATE
                    gols_pts_a = VALUES(gols_pts_a),
                    gols_pts_b = VALUES(gols_pts_b),
                    pontos_sala_a = VALUES(pontos_sala_a),
                    pontos_sala_b = VALUES(pontos_sala_b)'
            );
            $placar->execute([
                'id_jogo' => $idJogo,
                'gols_a' => $pontosA,
                'gols_b' => $pontosB,
                'pontos_a' => $pontosSalaA,
                'pontos_b' => $pontosSalaB,
            ]);

            $atualizarJogo = $this->pdo->prepare(
                "UPDATE jogo
                 SET status = 'finalizado', id_time_vencedor = :vencedor
                 WHERE id = :id"
            );
            $atualizarJogo->execute(['vencedor' => $vencedor, 'id' => $idJogo]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function buscarPorId(int $idJogo): ?array
    {
        $stmt = $this->pdo->prepare(
            $this->consultaJogos() . ' WHERE j.id = :id'
        );
        $stmt->execute(['id' => $idJogo]);
        $jogo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $jogo ?: null;
    }

    public function alterarPlacarAoVivo(int $idJogo, string $lado, string $operacao): array
    {
        if (!in_array($lado, ['a', 'b'], true)) {
            throw new DomainException('Selecione um time válido.');
        }
        if (!in_array($operacao, ['incrementar', 'decrementar'], true)) {
            throw new DomainException('Selecione uma operação válida.');
        }

        $coluna = $lado === 'a' ? 'gols_pts_a' : 'gols_pts_b';
        $this->pdo->beginTransaction();

        try {
            $jogoStmt = $this->pdo->prepare(
                'SELECT j.id_timeA, j.id_timeB, j.status,
                        m.pts_vitoria, m.pts_empate, m.pts_derrota
                 FROM jogo j
                 INNER JOIN modalidade m ON m.id = j.id_modalidade
                 WHERE j.id = :id
                 FOR UPDATE'
            );
            $jogoStmt->execute(['id' => $idJogo]);
            $jogo = $jogoStmt->fetch(PDO::FETCH_ASSOC);
            if (!$jogo) {
                throw new DomainException('Jogo não encontrado.');
            }

            $placarStmt = $this->pdo->prepare(
                'INSERT INTO placar
                    (id_jogo, gols_pts_a, gols_pts_b, pontos_sala_a, pontos_sala_b)
                 VALUES
                    (:id_jogo, 0, 0, 0, 0)
                 ON DUPLICATE KEY UPDATE id_jogo = VALUES(id_jogo)'
            );
            $placarStmt->execute(['id_jogo' => $idJogo]);

            if ($operacao === 'incrementar') {
                $atualizar = $this->pdo->prepare(
                    "UPDATE placar
                     SET {$coluna} = {$coluna} + 1
                     WHERE id_jogo = :id_jogo"
                );
            } else {
                $atualizar = $this->pdo->prepare(
                    "UPDATE placar
                     SET {$coluna} = {$coluna} - 1
                     WHERE id_jogo = :id_jogo AND {$coluna} > 0"
                );
            }
            $atualizar->execute(['id_jogo' => $idJogo]);

            if ($operacao === 'decrementar' && $atualizar->rowCount() === 0) {
                throw new DomainException('O placar não pode ficar negativo.');
            }

            $placarAtual = $this->pdo->prepare(
                'SELECT gols_pts_a, gols_pts_b FROM placar WHERE id_jogo = :id_jogo'
            );
            $placarAtual->execute(['id_jogo' => $idJogo]);
            $resultado = $placarAtual->fetch(PDO::FETCH_ASSOC);

            if ($jogo['status'] === 'finalizado') {
                $pontosA = (int) $jogo['pts_empate'];
                $pontosB = (int) $jogo['pts_empate'];
                $vencedor = null;

                if ((int) $resultado['gols_pts_a'] > (int) $resultado['gols_pts_b']) {
                    $pontosA = (int) $jogo['pts_vitoria'];
                    $pontosB = (int) $jogo['pts_derrota'];
                    $vencedor = (int) $jogo['id_timeA'];
                } elseif ((int) $resultado['gols_pts_b'] > (int) $resultado['gols_pts_a']) {
                    $pontosA = (int) $jogo['pts_derrota'];
                    $pontosB = (int) $jogo['pts_vitoria'];
                    $vencedor = (int) $jogo['id_timeB'];
                }

                $atualizarPontos = $this->pdo->prepare(
                    'UPDATE placar
                     SET pontos_sala_a = :pontos_a, pontos_sala_b = :pontos_b
                     WHERE id_jogo = :id_jogo'
                );
                $atualizarPontos->execute([
                    'pontos_a' => $pontosA,
                    'pontos_b' => $pontosB,
                    'id_jogo' => $idJogo,
                ]);

                $atualizarVencedor = $this->pdo->prepare(
                    'UPDATE jogo SET id_time_vencedor = :vencedor WHERE id = :id_jogo'
                );
                $atualizarVencedor->execute([
                    'vencedor' => $vencedor,
                    'id_jogo' => $idJogo,
                ]);
            }

            $this->pdo->commit();

            return [
                'gols_pts_a' => (int) $resultado['gols_pts_a'],
                'gols_pts_b' => (int) $resultado['gols_pts_b'],
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function listar(?string $dia = null): array
    {
        $sql = $this->consultaJogos();
        if ($dia !== null) {
            $sql .= ' WHERE j.dia = :dia';
        }
        $sql .= ' ORDER BY j.dia, j.horario, j.id';

        $stmt = $this->pdo->prepare($sql);
        if ($dia !== null) {
            $stmt->execute(['dia' => $dia]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMeusJogos(int $idUsuario): array
    {
        $stmt = $this->pdo->prepare(
            $this->consultaJogos() . '
             INNER JOIN time_jogador tj
                ON tj.id_time = j.id_timeA OR tj.id_time = j.id_timeB
             WHERE tj.id_usuario = :id_usuario
             ORDER BY j.dia, j.horario, j.id'
        );
        $stmt->execute(['id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarEscalacoes(array $idsJogos): array
    {
        $idsJogos = array_values(array_unique(array_filter(
            array_map('intval', $idsJogos),
            static function (int $id): bool {
                return $id > 0;
            }
        )));
        if ($idsJogos === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($idsJogos), '?'));
        $stmt = $this->pdo->prepare(
            'SELECT j.id AS id_jogo, t.id AS id_time, u.nome
             FROM jogo j
             INNER JOIN `time` t ON t.id = j.id_timeA OR t.id = j.id_timeB
             INNER JOIN time_jogador tj ON tj.id_time = t.id
             INNER JOIN usuario u ON u.id = tj.id_usuario
             WHERE j.id IN (' . $placeholders . ')
             ORDER BY u.nome'
        );
        $stmt->execute($idsJogos);

        $escalacoes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $jogador) {
            $escalacoes[(int) $jogador['id_jogo']][(int) $jogador['id_time']][] = $jogador['nome'];
        }
        return $escalacoes;
    }

    public function ranking(): array
    {
        return $this->pdo->query(
            "SELECT s.id, s.nome, COALESCE(SUM(resultado.pontos), 0) AS total_pontos
             FROM sala s
             LEFT JOIN (
                 SELECT t.id_sala, p.pontos_sala_a AS pontos
                 FROM jogo j
                 INNER JOIN `time` t ON t.id = j.id_timeA
                 INNER JOIN placar p ON p.id_jogo = j.id
                 WHERE j.status = 'finalizado'
                 UNION ALL
                 SELECT t.id_sala, p.pontos_sala_b AS pontos
                 FROM jogo j
                 INNER JOIN `time` t ON t.id = j.id_timeB
                 INNER JOIN placar p ON p.id_jogo = j.id
                 WHERE j.status = 'finalizado'
             ) resultado ON resultado.id_sala = s.id
             WHERE s.nome <> 'Diretoria'
             GROUP BY s.id, s.nome
             ORDER BY total_pontos DESC, s.nome"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function consultaJogos(): string
    {
        return 'SELECT j.id, j.id_timeA, j.id_timeB, j.dia, j.horario, j.quadra, j.status,
                       m.nome AS modalidade, m.genero,
                       sA.nome AS sala_a, sB.nome AS sala_b,
                       p.gols_pts_a, p.gols_pts_b
                FROM jogo j
                INNER JOIN modalidade m ON m.id = j.id_modalidade
                INNER JOIN `time` tA ON tA.id = j.id_timeA
                INNER JOIN sala sA ON sA.id = tA.id_sala
                INNER JOIN `time` tB ON tB.id = j.id_timeB
                INNER JOIN sala sB ON sB.id = tB.id_sala
                LEFT JOIN placar p ON p.id_jogo = j.id';
    }
}
