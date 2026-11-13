
<?php
include_once '../../../Db/database.php';

class UsuarioModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarAluno($nome, $rm, $id_sala)
    {
        try {
            $senha = null;
            $tipo = "aluno";

            $sql = "INSERT INTO usuario (nome, rm, senha, tipo, id_sala) VALUES (:nome, :rm, :senha, :tipo, :id_sala)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':rm', $rm);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':id_sala', $id_sala);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }


public function cadastrarRepresentante($nome, $rm, $senha, $id_sala)
{
    try {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM usuario
             WHERE id_sala = :id_sala
             AND tipo = 'representante'"
        );

        $stmt->bindParam(':id_sala', $id_sala);
        $stmt->execute();

        if ($stmt->fetchColumn() >= 2) {
            return false;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $tipo = "representante";

        $sql = "INSERT INTO usuario (nome, rm, senha, tipo, id_sala)
                VALUES (:nome, :rm, :senha, :tipo, :id_sala)";

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
            $sql = "SELECT * FROM usuario WHERE rm = :rm AND tipo = 'representante'";
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

    public function buscarAlunoPorRM($rm)
    {
        try {
            $sql = "SELECT id, nome, rm, id_sala FROM usuario WHERE rm = :rm AND tipo = 'aluno'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':rm', $rm);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }


public function listarAlunosPorSala($id_sala)
{
    try {
        $sql = "SELECT id, nome, rm FROM usuario
                WHERE id_sala = :id_sala
                AND tipo = 'aluno'
                ORDER BY nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_sala', $id_sala);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}


}
