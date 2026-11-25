
<?php

require_once __DIR__ . '/../Model/JogoModel.php';

class JogoController
{
    private JogoModel $jogoModel;

    public function __construct(PDO $pdo)
    {
        $this->jogoModel = new JogoModel($pdo);
    }

    // Cadastra um confronto
    public function cadastrar(array $dados): string
    {
        $modalidade = filter_var(
            $dados['id_modalidade'] ?? null,
            FILTER_VALIDATE_INT
        );

        $salaA = filter_var(
            $dados['id_salaA'] ?? null,
            FILTER_VALIDATE_INT
        );

        $salaB = filter_var(
            $dados['id_salaB'] ?? null,
            FILTER_VALIDATE_INT
        );

        $dia = is_string($dados['dia'] ?? null) ? trim($dados['dia']) : '';
        $horario = is_string($dados['horario'] ?? null) ? trim($dados['horario']) : '';
        $quadra = is_string($dados['quadra'] ?? null) ? trim($dados['quadra']) : '';

        // Valida os identificadores
        if (
            $modalidade === false || $modalidade < 1 ||
            $salaA === false || $salaA < 1 ||
            $salaB === false || $salaB < 1
        ) {
            return 'Selecione uma modalidade e duas salas válidas.';
        }

        // Valida a data
        $data = DateTime::createFromFormat('!Y-m-d', $dia);

        if (!$data || $data->format('Y-m-d') !== $dia) {
            return 'Informe uma data válida.';
        }

        // Valida o horário
        $hora = DateTime::createFromFormat('!H:i', $horario);

        if (!$hora || $hora->format('H:i') !== $horario) {
            return 'Informe um horário válido.';
        }

        // Impede a escolha da mesma sala
        if ($salaA === $salaB) {
            return 'Escolha duas salas diferentes para o confronto.';
        }

        if ($quadra === '') {
            return 'Informe a quadra do confronto.';
        }

        try {
            if (
                $this->jogoModel->cadastrar(
                    $modalidade,
                    $salaA,
                    $salaB,
                    $dia,
                    $horario,
                    $quadra
                )
            ) {
                return 'Confronto cadastrado com sucesso.';
            }

            return 'Não foi possível cadastrar o confronto. Verifique os dados informados.';
        } catch (DomainException $e) {
            return $e->getMessage();
        } catch (PDOException $e) {
            error_log('Erro ao cadastrar confronto: ' . $e->getMessage());
            return 'Não foi possível cadastrar o confronto. Verifique os dados informados.';
        } catch (RuntimeException $e) {
            error_log('Erro ao cadastrar confronto: ' . $e->getMessage());
            return 'Não foi possível cadastrar o confronto.';
        }
    }

    // Lista as salas
    public function listarSalas(): array
    {
        return $this->jogoModel->listarSalas();
    }

    // Lista as modalidades
    public function listarModalidades(): array
    {
        return $this->jogoModel->listarModalidades();
    }

    // Lista os confrontos
    public function listar(?string $dia = null): array
    {
        return $this->jogoModel->listar($dia);
    }

    public function listarMeusJogos(int $idUsuario): array
    {
        return $this->jogoModel->listarMeusJogos($idUsuario);
    }

    public function listarEscalacoes(array $idsJogos): array
    {
        return $this->jogoModel->listarEscalacoes($idsJogos);
    }

    public function ranking(): array
    {
        return $this->jogoModel->ranking();
    }

    public function registrarPlacar(array $dados): string
    {
        $idJogo = filter_var($dados['id_jogo'] ?? null, FILTER_VALIDATE_INT);
        $pontosA = filter_var($dados['gols_pts_a'] ?? null, FILTER_VALIDATE_INT);
        $pontosB = filter_var($dados['gols_pts_b'] ?? null, FILTER_VALIDATE_INT);

        if (
            $idJogo === false || $idJogo < 1 ||
            $pontosA === false || $pontosA < 0 ||
            $pontosB === false || $pontosB < 0
        ) {
            return 'Informe um jogo e um placar válidos.';
        }

        try {
            $this->jogoModel->registrarPlacar($idJogo, $pontosA, $pontosB);
            return 'Placar registrado com sucesso.';
        } catch (DomainException $e) {
            return $e->getMessage();
        } catch (PDOException $e) {
            error_log('Erro ao registrar placar: ' . $e->getMessage());
            return 'Não foi possível registrar o placar.';
        }
    }

    public function buscarPorId(int $idJogo): ?array
    {
        if ($idJogo < 1) {
            return null;
        }

        return $this->jogoModel->buscarPorId($idJogo);
    }

    public function alterarPlacarAoVivo(array $dados): array
    {
        $idJogo = filter_var($dados['id_jogo'] ?? null, FILTER_VALIDATE_INT);
        $lado = is_string($dados['lado'] ?? null) ? $dados['lado'] : '';
        $operacao = is_string($dados['operacao'] ?? null) ? $dados['operacao'] : '';

        if ($idJogo === false || $idJogo < 1) {
            throw new DomainException('Informe um jogo válido.');
        }

        return $this->jogoModel->alterarPlacarAoVivo($idJogo, $lado, $operacao);
    }
}
