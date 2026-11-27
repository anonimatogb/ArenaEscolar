
<?php
require_once dirname(__DIR__, 2) . '/DB/database.php';

class UsuarioModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarAluno($nome, $rm, $id_sala, $genero)
    {
        try {
            $senha = null;
            $tipo = "aluno";

            $sql = "INSERT INTO usuario (nome, rm, senha, tipo, id_sala, genero) VALUES (:nome, :rm, :senha, :tipo, :id_sala, :genero)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':rm', $rm);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->bindParam(':genero', $genero);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }


    public function cadastrarRepresentante($nome, $rm, $id_sala, $genero, $senha)
    {
        if (!is_string($senha) || strlen($senha) < 4) {
            return false;
        }

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


            $tipo = "representante";
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuario (nome, rm, senha, tipo, id_sala, genero)
                VALUES (:nome, :rm, :senha, :tipo, :id_sala, :genero)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':rm', $rm);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->bindParam(':genero', $genero);

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

            if (!$usuario) {
                return false;
            }

            if (!empty($usuario['senha'])) {
                return password_verify($senha, $usuario['senha']) ? $usuario : false;
            }

            if (strlen($senha) < 4) {
                return false;
            }

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $atualizar = $this->pdo->prepare(
                "UPDATE usuario SET senha = :senha
                 WHERE id = :id AND tipo = 'representante'
                   AND (senha IS NULL OR senha = '')"
            );
            $atualizar->execute(['senha' => $senhaHash, 'id' => $usuario['id']]);
            if ($atualizar->rowCount() !== 1) {
                return false;
            }

            $usuario['senha'] = $senhaHash;
            return $usuario;
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

    public function buscarRepresentantePorRM($rm)
    {
        try {
            $sql = "SELECT id, nome, rm, id_sala
                    FROM usuario
                    WHERE rm = :rm AND tipo = 'representante'";
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
            $sql = "SELECT id, nome, rm, genero, tipo
                FROM usuario
                WHERE id_sala = :id_sala
                AND tipo IN ('aluno', 'representante')
                ORDER BY nome";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function obterAlunoPorId($id_aluno)
    {
        try {
            $sql = "SELECT id, nome, rm, genero FROM usuario WHERE id = :id AND tipo = 'aluno'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id_aluno);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function atualizarAluno($id_aluno, $nome, $rm, $genero)
    {
        try {
            $sql = "UPDATE usuario SET nome = :nome, rm = :rm, genero = :genero WHERE id = :id AND tipo = 'aluno'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':rm', $rm);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':id', $id_aluno);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function atualizarRepresentante($id_representante, $nome, $senha)
    {
        if (!is_string($senha) || strlen($senha) < 4) {
            return false;
        }

        try {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "UPDATE usuario SET nome = :nome, senha = :senha WHERE id = :id AND tipo = 'representante'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':id', $id_representante);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obterRepresentantePorId($id_representante)
    {
        try {
            $sql = "SELECT id, nome, rm FROM usuario WHERE id = :id AND tipo = 'representante'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id_representante);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function cadastrarAdministrador($nome_usuario, $senha)
    {
        try {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuario (nome, rm, senha, tipo, id_sala)
                SELECT :nome, NULL, :senha, 'admin', id
                FROM sala
                WHERE nome = 'Diretoria'
                LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome_usuario);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            return false;
        }
    }


    public function deletar($id)
    {
        try {
            $sql = "DELETE FROM usuario WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
