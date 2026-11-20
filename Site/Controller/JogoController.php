
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

        $dia = trim($dados['dia'] ?? '');
        $horario = trim($dados['horario'] ?? '');

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

        // Junta data e horário para armazenar no DATETIME
        $dataHora = $dia . ' ' . $horario . ':00';

        if (
            $this->jogoModel->cadastrar(
                $modalidade,
                $salaA,
                $salaB,
                $dataHora
            )
        ) {
            return 'Confronto cadastrado com sucesso.';
        }

        return 'Não foi possível cadastrar o confronto. Verifique os dados informados.';
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
    public function listar(): array
    {
        return $this->jogoModel->listar();
    }
}
