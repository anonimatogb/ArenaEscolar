<?php
require_once __DIR__ . '/../Model/ModalidadeM.php';

class ModalidadeController {

    public function cadastrar() {
        $mensagem = "";
        $erroListagem = "";
        $formAction = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nome = $_POST['nome'] ?? '';
            $quantidadeEnviada = $_POST['quantidade'] ?? '';
            $genero = $_POST['genero'] ?? '';
            $quantidade = is_string($quantidadeEnviada)
                ? filter_var($quantidadeEnviada, FILTER_VALIDATE_INT)
                : false;

            if (
                is_string($nome)
                && trim($nome) !== ''
                && $quantidade !== false
                && $quantidade > 0
                && is_string($genero)
                && in_array($genero, ['Masc', 'Fem', 'Misto'], true)
            ) {
                $modalidade = new Modalidade();
                $modalidade->nome = trim($nome);
                $modalidade->quantidade = $quantidade;
                $modalidade->genero = $genero;

                try {
                    if ($modalidade->salvar()) {
                        $mensagem = "Modalidade cadastrada com sucesso!";
                    } else {
                        $mensagem = "Não foi possível, Verifique se já existe.";
                    }
                } catch (PDOException $e) {
                    error_log('Erro ao cadastrar modalidade: ' . $e->getMessage());
                    $mensagem = "Não foi possível cadastrar a modalidade no banco de dados.";
                }
            } else {
                $mensagem = "Preencha todos os campos corretamente.";
            }
        }

        try {
            $modalidades = (new Modalidade())->listar();
        } catch (PDOException $e) {
            error_log('Erro ao listar modalidades: ' . $e->getMessage());
            $modalidades = [];
            $erroListagem = "Não foi possível carregar as modalidades do banco de dados.";
        }

        require __DIR__ . '/../View/Modalidade/modalidade.php';
    }
}