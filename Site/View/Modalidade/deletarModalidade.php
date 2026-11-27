<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/ModalidadeController.php';

$modalidadeController = new ModalidadeController($pdo);

$id = $_GET['id_modalidade'] ?? null;

if ($id !== null) {
    $deletado = $modalidadeController->deletar($id);

    if ($deletado) {
        header('Location: ../administrador/inicio.php?mensagem=Modalidade deletada com sucesso');
        exit();
    } else {
        header('Location: ../administrador/inicio.php?erro=Erro ao deletar a modalidade');
        exit();
    }
} else {
    header('Location: ../administrador/inicio.php?erro=ID da modalidade não fornecido');
    exit();
}
