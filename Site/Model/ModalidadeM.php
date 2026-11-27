<?php
require_once dirname(__DIR__, 2) . '/DB/database.php';

class Modalidade {
    private $conn;
    private $table_name = "modalidade";

    public $id;
    public $nome;   
    public $quantidade;  
    public $genero;

    public function __construct(?PDO $conn = null) {
        if ($conn !== null) {
            $this->conn = $conn;
        } elseif (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            $this->conn = $GLOBALS['pdo'];
        } else {
            require_once dirname(__DIR__, 2) . '/DB/database.php';
            $this->conn = $GLOBALS['pdo'];
        }
    }

    public function salvar() {
        $query = "INSERT INTO " . $this->table_name . " (nome, max_jogadores, genero) VALUES (:nome, :quantidade, :genero)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':nome' => $this->nome,
            ':quantidade' => $this->quantidade,
            ':genero' => $this->genero,
        ]);
    }

    public function existeComMesmasCaracteristicas()
{
    $query = "SELECT 1 FROM " . $this->table_name
        . " WHERE nome = :nome"
        . " AND genero = :genero"
        . " LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
        ':nome'       => $this->nome,
        ':genero'     => $this->genero,
    ]);

    return $stmt->fetchColumn() !== false;
}

    public function listar() {
        $query = "SELECT id, nome, max_jogadores AS quantidade, genero FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->query($query);

        return $stmt->fetchAll();
    }

     public function deletar($id)
    {
        try {
            $sql = "DELETE FROM modalidade WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
