
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
            'SELECT 1 FROM administrador WHERE id_unico = 1 LIMIT 1'
        );

        return $stmt->fetchColumn() !== false;
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
                'INSERT INTO administrador (id_unico, nome_usuario, senha)
                 VALUES (1, :nome, :senha)'
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
