
<?php
require_once dirname(__DIR__, 2) . '/DB/database.php';

class SalaModel
{
    private $pdo;
   
    public function __construct($pdo)
    {  
        $this->pdo = $pdo;
    }

    public function listarSalas()
    {
        try {
            $sql = "SELECT * FROM sala";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
