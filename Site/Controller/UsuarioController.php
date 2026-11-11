
<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';


class UsuarioController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    public function apenasum($id) {
        return $this->usuarioModel->apenasum($id);
    }

    public function buscarPorRM($rm) {
        return $this->usuarioModel->buscarPorRM($rm);
    }

    public function Cadastro($nome, $rm, $senha, $tipo, $sala_id) {
        return $this->usuarioModel->Cadastro($nome, $rm, $senha, $tipo, $sala_id);
    }

    public function login($rm, $senha) {
        return $this->usuarioModel->login($rm, $senha);
    }

    public function editar($nome, $senha, $id) {
        return $this->usuarioModel->editar($nome, $senha, $id);
    }
}
