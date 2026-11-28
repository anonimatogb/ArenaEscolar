<?php
require_once dirname(__DIR__, 2) . '/DB/database.php';

class SalaModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarSalas()
    {
        try {
            $sql = "SELECT * FROM sala";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarIntegrantesPorSala()
    {
        try {
            $sql = "
                SELECT
                    sala.id AS id_sala,
                    sala.nome AS nome_sala,
                    usuario.id AS id_usuario,
                    usuario.nome AS nome_aluno,
                    usuario.rm,
                    usuario.tipo,
                    usuario.genero
                FROM sala
                LEFT JOIN usuario
                    ON usuario.id_sala = sala.id
                ORDER BY
                    CASE sala.nome
                        WHEN '6° EF' THEN 1
                        WHEN '7° EF' THEN 2
                        WHEN '8° EF' THEN 3
                        WHEN '9° EF' THEN 4
                        WHEN '1° EM' THEN 5
                        WHEN '2° EM' THEN 6
                        WHEN '3° EM' THEN 7
                        WHEN 'Diretoria' THEN 8
                        ELSE 9
                    END,
                    usuario.nome
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }
      public function deletar($sala)
    {
        try {
            $sql = "DELETE FROM sala WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $sala, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }


    public function cadastrarsala($nome)
{
    try {
        $sql = "INSERT INTO sala (nome, pontos, qtdtimes)
                VALUES (:nome, :pontos, :qtdtimes)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':pontos', 0);
        $stmt->bindValue(':qtdtimes', 0);

        return $stmt->execute();

    } catch (PDOException $e) {
        error_log('Erro ao cadastrar sala: ' . $e->getMessage());
        return false;
    }
}


}