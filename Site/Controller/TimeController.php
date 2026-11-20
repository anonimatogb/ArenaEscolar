
<?php

require_once dirname(__DIR__) . '/Model/TimeModel.php';

class TimeController
{
    private TimeModel $timeModel;

    public function __construct(PDO $pdo)
    {
        $this->timeModel = new TimeModel($pdo);
    }

    // Busca a sala do representante
    public function buscarSalaRepresentante(int $idUsuario)
    {
        return $this->timeModel->buscarSalaRepresentante($idUsuario);
    }

    // Lista modalidades ainda não cadastradas pela sala
    public function listarModalidadesDisponiveis(int $idUsuario)
    {
        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new Exception("Representante não encontrado.");
        }

        $idSala = (int) $representante['id_sala'];

        return $this->timeModel->listarModalidadesDisponiveis($idSala);
    }

    // Lista alunos disponíveis para a modalidade
    public function listarAlunosDisponiveis(
        int $idUsuario,
        int $idModalidade
    ) {
        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new Exception("Representante não encontrado.");
        }

        if ($idModalidade <= 0) {
            throw new Exception("Modalidade inválida.");
        }

        $idSala = (int) $representante['id_sala'];

        // Confere se a modalidade ainda está disponível para a sala
        $modalidades = $this->timeModel->listarModalidadesDisponiveis($idSala);

        $modalidadeValida = false;

        foreach ($modalidades as $modalidade) {
            if ((int) $modalidade['id'] === $idModalidade) {
                $modalidadeValida = true;
                break;
            }
        }

        if (!$modalidadeValida) {
            throw new Exception(
                "Essa modalidade já foi cadastrada ou não existe."
            );
        }

        return $this->timeModel->listarAlunosDisponiveis(
            $idSala,
            $idModalidade
        );
    }

    // Cadastra o time
    public function cadastrarTime(
        int $idUsuario,
        int $idModalidade,
        array $alunos
    ) {
        if ($idUsuario <= 0) {
            throw new Exception("Usuário inválido.");
        }

        if ($idModalidade <= 0) {
            throw new Exception("Selecione uma modalidade válida.");
        }

        if (empty($alunos)) {
            throw new Exception("Selecione os alunos do time.");
        }

        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new Exception("Representante não encontrado.");
        }

        $idSala = (int) $representante['id_sala'];

        return $this->timeModel->cadastrarTime(
            $idSala,
            $idModalidade,
            $alunos
        );
    }
}
