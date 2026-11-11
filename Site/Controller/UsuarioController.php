<?php
require_once '../../Model/UsuarioModel.php';


class UsuarioController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new UsuarioModel($pdo);
    }
    public function cadastrarAluno($nome, $rm, $senha, $id_sala) {
        return $this->usuarioModel->cadastrarAluno($nome, $rm, $senha, $id_sala);
    }
}
    
?>