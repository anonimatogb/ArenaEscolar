
<?php

class AdministradorModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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

 
}
