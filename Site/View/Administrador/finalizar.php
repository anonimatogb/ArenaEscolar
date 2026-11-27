<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/JogoController.php';

$jogoController = new JogoController($pdo);

$id = $_GET['id_jogo'] ?? null;

if ($id !== null) {
    $finalizado = $jogoController->finalizar($id);

    if ($finalizado) {
        header('Location: inicio.php?mensagem=Usuario finalizado com sucesso');
        exit();
    } else {
        header('Location: inicio.php?erro=Erro ao finalizar o usuario');
        exit();
    }
} else {
    header('Location: inicio.php?erro=ID do jogo não fornecido');
    exit();
}
