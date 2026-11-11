<?php
include_once '../../../Db/database.php';

class UsuarioModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarAluno($nome, $rm, $senha, $id_sala)
    {
        try {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $tipo = "aluno";

            $sql = "INSERT INTO usuario (nome, rm, senha,tipo,id_sala) VALUES (:nome, :rm, :senha, :tipo, :id_sala)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':rm', $rm);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':id_sala', $id_sala);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    public function login($rm, $senha)
    {
        try {
            $sql = "SELECT * FROM usuario WHERE rm = :rm";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':rm', $rm);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                return $usuario;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
}
