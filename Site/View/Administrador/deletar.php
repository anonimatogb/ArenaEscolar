<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';

$usuarioController = new UsuarioController($pdo);

$id = $_GET['id_aluno'] ?? null;

if ($id !== null) {
    $deletado = $usuarioController->deletar($id);

    if ($deletado) {
        header('Location: salas.php?mensagem=Usuario deletado com sucesso');
        exit();
    } else {
        header('Location: salas.php?erro=Erro ao deletar o usuario');
        exit();
    }
} else {
    header('Location: salas.php?erro=ID do usuario não fornecido');
    exit();
}
