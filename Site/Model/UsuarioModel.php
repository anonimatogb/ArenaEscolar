
<?php
include_once __DIR__ . '/../Db/Database.php';


class UsuarioModel {
    private $pdo;

    public function __construct() {
        $this->pdo = $GLOBALS['pdo'];
    }

    public function apenasum($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorRM($rm) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE rm = :rm");
        $stmt->bindParam(':rm', $rm);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function Cadastro($nome, $rm, $senha, $tipo, $sala_id) {
        try {

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (nome, rm, senha, tipo, sala_id)
                VALUES (:nome, :rm, :senha, :tipo, :sala_id)
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':rm', $rm);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
            $stmt->bindParam(':sala_id', $sala_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    public function login($rm, $senha) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE rm = :rm");
        $stmt->bindParam(':rm', $rm);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        } else {
            return false;
        }
    }

    public function editar($nome, $senha, $id) {
        try {

            if (!empty($senha)) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            } else {
                $senhaHash = null;
            }

            $stmt = $this->pdo->prepare("
                UPDATE usuarios
                SET nome = COALESCE(:nome, nome),
                    senha = COALESCE(:senha, senha)
                WHERE id = :id
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }
}
