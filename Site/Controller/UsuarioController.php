<?php
require_once '../Model/UsuarioModel.php';


class UsuarioController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    public function apenasum($id) {
        return $this->usuarioModel->apenasum($id);
    }

    public function Cadastro($nome, $email, $senha, $tipo) {
        return $this->usuarioModel->Cadastro($nome, $email, $senha, $tipo);
    }

    public function login($email, $senha) {
        return $this->usuarioModel->login($email, $senha);
    }
    public function editar($nome, $email, $senha, $id) {
        return $this->usuarioModel->editar($nome, $email, $senha, $id);
    }
}
