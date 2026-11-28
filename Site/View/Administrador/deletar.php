<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';
require_once __DIR__ . '/../../Controller/SalaController.php';

$usuarioController = new UsuarioController($pdo);
$salaController = new SalaController($pdo);


$sala = $_GET['id_sala'] ?? null;
$id = $_GET['id_aluno'] ?? null;

if (!empty($id)) {


    if ($id !== null) {
        $deletado = $usuarioController->deletar($id);

        if ($deletado) {
            header('Location: salas.php?mensagem=Usuario deletado com sucesso');
            exit();
        } else {
            header('Location: salas.php?erro=Erro ao deletar o usuario');
            exit();
        }
    }
} elseif (!empty($sala)) {

    if ($sala !== null) {
        $deletarsala = $salaController->deletar($sala);

        if ($deletarsala) {
            header('Location: incio.php?mensagem=Sala deletada com sucesso');
            exit();
        } else {
            header('Location: inicio.php?erro=Erro ao deletar sala');
            exit();
        }
    }
} else {
    header('Location: inicio.php?erro=ID não fornecido');
    exit();
}
