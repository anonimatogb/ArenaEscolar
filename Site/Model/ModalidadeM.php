<?php
require_once __DIR__ . '/../../DB/database.php';

class Modalidade {
    private $conn;
    private $table_name = "modalidade";

    public $id;
    public $nome;
    public $quantidade;
    public $genero;

    public function __construct() {
        $this->conn = $GLOBALS['pdo'];
    }

    public function salvar() {
        $query = "INSERT INTO " . $this->table_name . " (nome, quantidade, genero) VALUES (:nome, :quantidade, :genero)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':nome' => $this->nome,
            ':quantidade' => $this->quantidade,
            ':genero' => $this->genero,
        ]);
    }

    public function existeComMesmasCaracteristicas() {
        $query = "SELECT 1 FROM " . $this->table_name
            . " WHERE nome = :nome AND quantidade = :quantidade AND genero = :genero LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':nome' => $this->nome,
            ':quantidade' => $this->quantidade,
            ':genero' => $this->genero,
        ]);

        return $stmt->fetchColumn() !== false;
    }

    public function listar() {
        $query = "SELECT id, nome, quantidade, genero FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->query($query);

        return $stmt->fetchAll();
    }
}