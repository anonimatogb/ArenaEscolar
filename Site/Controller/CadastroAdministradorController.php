<?php

require_once __DIR__ . '/../Model/AdministradorModel.php';
require_once dirname(__DIR__, 2) . '/Db/database.php';

$model = new AdministradorModel($pdo);
$mensagem = '';
$cadastrado = false;
$formularioDisponivel = false;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($model->existeAdministrador()) {
            $mensagem = 'O administrador do sistema já foi cadastrado.';
        } else {
            $nomeUsuario = trim($_POST['nome_usuario'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if ($nomeUsuario === '' || $senha === '') {    
                $mensagem = 'Preencha o nome de usuário e a senha.';
            } elseif (strlen($nomeUsuario) > 255) {
                $mensagem = 'O nome de usuário deve ter no máximo 255 caracteres.';
            } elseif ($model->cadastrar($nomeUsuario, $senha)) {
                $cadastrado = true;
                $mensagem = 'Administrador cadastrado com sucesso.';
            } else {
                $mensagem = 'O cadastro não está disponível ou não pôde ser concluído.';
            }
        }
    } elseif ($model->existeAdministrador()) {
        $mensagem = 'O administrador do sistema já foi cadastrado.';
    } else {
        $formularioDisponivel = true;
    }
} catch (PDOException $e) {
    // Mensagem genérica para falhas de consulta; detalhes ficam apenas no servidor.
    error_log('Falha ao consultar o cadastro de administrador.');
    $mensagem = 'Não foi possível verificar a disponibilidade do cadastro.';
}

require __DIR__ . '/../View/Administrador/CadastroAdministrador.php';
