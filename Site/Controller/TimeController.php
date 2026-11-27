
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

    public function listarModalidadesParaCadastroAluno(int $idUsuario): array
    {
        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new Exception("Representante não encontrado.");
        }

        return $this->timeModel->listarModalidadesParaCadastroAluno(
            (int) $representante['id_sala']
        );
    }

    public function listarTimesDaSala(int $idUsuario): array
    {
        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new Exception("Representante não encontrado.");
        }

        return $this->timeModel->listarTimesPorSala(
            (int) $representante['id_sala']
        );
    }

    public function listarModalidadesParaEditarAluno(
        int $idUsuario,
        int $idAluno
    ): array {
        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new Exception("Representante não encontrado.");
        }

        return $this->timeModel->listarModalidadesParaEditarAluno(
            (int) $representante['id_sala'],
            $idAluno
        );
    }

    public function cadastrarAlunoNaModalidade(
        int $idUsuario,
        string $nome,
        string $rm,
        string $genero,
        int $idModalidade
    ): int {
        if ($idUsuario <= 0) {
            throw new DomainException("Usuário inválido.");
        }

        if ($nome === '' || $rm === '') {
            throw new DomainException("Informe o nome e o RM do aluno.");
        }

        if (!in_array($genero, ['masculino', 'feminino'], true)) {
            throw new DomainException("Selecione um gênero válido.");
        }

        if ($idModalidade <= 0) {
            throw new DomainException("Selecione uma modalidade válida.");
        }

        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new DomainException("Representante não encontrado.");
        }

        return $this->timeModel->cadastrarAlunoNaModalidade(
            (int) $representante['id_sala'],
            $idModalidade,
            $nome,
            $rm,
            $genero
        );
    }

    public function atualizarAlunoEModalidades(
        int $idUsuario,
        int $idAluno,
        string $nome,
        string $rm,
        string $genero,
        array $modalidades
    ): void {
        if ($idUsuario <= 0 || $idAluno <= 0) {
            throw new DomainException("Usuário ou aluno inválido.");
        }

        if (trim($nome) === '' || trim($rm) === '') {
            throw new DomainException("Informe o nome e o RM do aluno.");
        }

        if (!in_array($genero, ['masculino', 'feminino'], true)) {
            throw new DomainException("Selecione um gênero válido.");
        }

        $idsModalidades = [];
        foreach ($modalidades as $idModalidade) {
            if (!is_string($idModalidade) && !is_int($idModalidade)) {
                throw new DomainException("Selecione modalidades válidas.");
            }

            $idValidado = filter_var($idModalidade, FILTER_VALIDATE_INT);
            if ($idValidado === false || $idValidado <= 0) {
                throw new DomainException("Selecione modalidades válidas.");
            }

            $idsModalidades[] = $idValidado;
        }

        $representante = $this->timeModel->buscarSalaRepresentante($idUsuario);

        if (!$representante) {
            throw new DomainException("Representante não encontrado.");
        }

        $this->timeModel->atualizarAlunoEModalidades(
            (int) $representante['id_sala'],
            $idAluno,
            $nome,
            $rm,
            $genero,
            $idsModalidades
        );
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


   public function cadastrarAlunoEmModalidades(
    $idUsuario,
    $nome,
    $rm,
    $genero,
    $modalidadesSelecionadas
) {
    // Busca a sala vinculada ao representante
    $sala = $this->timeModel->buscarSalaRepresentante($idUsuario);

    if (!$sala) {
        throw new DomainException(
            'Sala do representante não encontrada.'
        );
    }

    // Cadastra o aluno nas modalidades selecionadas
    return $this->timeModel->cadastrarAlunoEmModalidades(
        (int) $sala['id_sala'],
        $nome,
        $rm,
        $genero,
        $modalidadesSelecionadas
    );
}


}
