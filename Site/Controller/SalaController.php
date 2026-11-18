
<?php
require_once __DIR__ . '/../Model/SalaModel.php';

class SalaController {
    private $salaModel;
  
    public function __construct($pdo) {  
        $this->salaModel = new SalaModel($pdo);
    }

    public function listarSalas() {
        return $this->salaModel->listarSalas();
    }
}

?>
