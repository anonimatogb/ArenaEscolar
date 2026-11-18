
<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new UsuarioModel($pdo);  
    }   

    public function cadastrarAluno($nome, $rm, $id_sala) {
        return $this->usuarioModel->cadastrarAluno($nome, $rm, $id_sala);
    }

    public function cadastrarRepresentante($nome, $rm, $senha, $id_sala) {
    return $this->usuarioModel->cadastrarRepresentante($nome, $rm, $senha, $id_sala);
}

    public function login($rm, $senha) {
        return $this->usuarioModel->login($rm, $senha);
    }

    public function buscarAlunoPorRM($rm) {
        return $this->usuarioModel->buscarAlunoPorRM($rm);
    }


public function listarAlunosPorSala($id_sala) {
    return $this->usuarioModel->listarAlunosPorSala($id_sala);
}


}
?>
