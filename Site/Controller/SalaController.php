<?php
require_once '../../Model/SalaModel.php';


class SalaController {
    private $salaModel;

    public function __construct($pdo) {
        $this->salaModel = new SalaModel($pdo);
    }
    public function cadastrarSala($nome) {
        return $this->salaModel->cadastrarSala($nome);
    }
    public function listarSalas() {
        return $this->salaModel->listarSalas();
    }
}
    
?>