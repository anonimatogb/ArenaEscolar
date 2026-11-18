<?php
session_start();

if (!isset($_SESSION['id_administrador']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: ../Administrador/Login.php');
    exit();
}

require_once __DIR__ . '/../../Controller/ModalidadeC.php';

$controller = new ModalidadeController();
$controller->cadastrar();
     
