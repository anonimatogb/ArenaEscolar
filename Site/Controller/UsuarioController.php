
<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new UsuarioModel($pdo);  
    }   

    public function cadastrarAluno($nome, $rm, $id_sala, $genero) {
        return $this->usuarioModel->cadastrarAluno($nome, $rm, $id_sala, $genero);
    }

    public function cadastrarRepresentante($nome, $rm, $id_sala, $genero, $senha) {
        return $this->usuarioModel->cadastrarRepresentante($nome, $rm, $id_sala, $genero, $senha);
    }

    public function login($rm, $senha, $confirmacaoSenha = '') {
        return $this->usuarioModel->login($rm, $senha, $confirmacaoSenha);
    }

    public function buscarAlunoPorRM($rm) {
        return $this->usuarioModel->buscarAlunoPorRM($rm);
    }


public function listarAlunosPorSala($id_sala) {
    return $this->usuarioModel->listarAlunosPorSala($id_sala);
}

public function obterAlunoPorId($id_aluno) {
    return $this->usuarioModel->obterAlunoPorId($id_aluno);
}

public function atualizarAluno($id_aluno, $nome, $rm, $genero) {
    return $this->usuarioModel->atualizarAluno($id_aluno, $nome, $rm, $genero);
}

public function atualizarRepresentante($id_representante, $nome, $senha) {
    return $this->usuarioModel->atualizarRepresentante($id_representante, $nome, $senha);
}
public function obterRepresentantePorId($id_representante) {
    return $this->usuarioModel->obterRepresentantePorId($id_representante);
}

public function cadastrarAdministrador($nome, $senha) {
    return $this->usuarioModel->cadastrarAdministrador($nome, $senha);
}


}
?>
