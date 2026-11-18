
<?php

class AdministradorModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function existeAdministrador(): bool
    {
        $stmt = $this->pdo->query(
            "SELECT 1 FROM usuario WHERE tipo IN ('admin', 'ADM') LIMIT 1"
        );

        return $stmt->fetchColumn() !== false;
    }

    public function login(string $nomeUsuario, string $senha)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nome, senha
             FROM usuario
             WHERE nome = :nome_usuario
               AND tipo IN ('admin', 'ADM')
             LIMIT 1"
        );
        $stmt->execute(['nome_usuario' => $nomeUsuario]);
        $administrador = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($administrador && password_verify($senha, $administrador['senha'])) {
            return $administrador;
        }

        return false;
    }

    public function cadastrar(string $nomeUsuario, string $senha): bool
    {
        if ($this->existeAdministrador()) {  
            return false;  
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);

        if ($hash === false) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO usuario (nome, rm, senha, tipo, id_sala)
                 VALUES (:nome, NULL, :senha, 'admin', NULL)"
            );

            return $stmt->execute([
                'nome' => $nomeUsuario,
                'senha' => $hash
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }
}
