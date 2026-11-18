<?php
require_once __DIR__ . '/../Model/JogoModel.php';

class JogoController
{
    private JogoModel $jogoModel;

    public function __construct(PDO $pdo)
    {
        $this->jogoModel = new JogoModel($pdo);
    }

    public function cadastrar(array $dados): string
    {
        $modalidade = filter_var($dados['id_modalidade'] ?? null, FILTER_VALIDATE_INT);
        $salaA = filter_var($dados['id_salaA'] ?? null, FILTER_VALIDATE_INT);
        $salaB = filter_var($dados['id_salaB'] ?? null, FILTER_VALIDATE_INT);
        $dia = $dados['dia'] ?? '';
        $horario = $dados['horario'] ?? '';

        $data = DateTime::createFromFormat('Y-m-d', $dia);
        $hora = DateTime::createFromFormat('H:i', $horario);

        if (
            $modalidade === false || $modalidade < 1 ||
            $salaA === false || $salaA < 1 ||
            $salaB === false || $salaB < 1 ||
            !$data || $data->format('Y-m-d') !== $dia ||
            !$hora || $hora->format('H:i') !== $horario
        ) {
            return 'Preencha modalidade, salas, dia e horário corretamente.';
        }

        if ($salaA === $salaB) {
            return 'Escolha duas salas diferentes para o confronto.';
        }

        if ($this->jogoModel->cadastrar($modalidade, $salaA, $salaB, $dia, $horario)) {
            return 'Confronto cadastrado com sucesso.';
        }

        return 'Não foi possível cadastrar. Confira se as salas são da mesma divisão e se a modalidade existe.';
    }

    public function listarSalas(): array
    {
        return $this->jogoModel->listarSalas();
    }

    public function listarModalidades(): array
    {
        return $this->jogoModel->listarModalidades();
    }

    public function listar(): array
    {
        return $this->jogoModel->listar();
    }
}
