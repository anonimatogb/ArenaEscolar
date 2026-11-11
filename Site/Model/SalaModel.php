<?php
include_once '../../../Db/database.php';

class SalaModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarSala($nome)
    {
        try {
           
            $sql = "INSERT INTO sala (nome,pontos) VALUES (:nome, 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
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
