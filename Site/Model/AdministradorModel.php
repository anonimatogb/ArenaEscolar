
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

    public function reiniciarAnoLetivo(): void
    {
        $this->pdo->beginTransaction();

        try {
            foreach (['placar', 'jogo', 'time_jogador', '`time`'] as $tabela) {
                $this->pdo->exec("DELETE FROM {$tabela}");
            }

            $this->pdo->exec(
                "DELETE FROM usuario WHERE tipo IN ('aluno', 'representante')"
            );
            $this->pdo->exec('UPDATE sala SET pontos = 0, qtdtimes = 0');

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
