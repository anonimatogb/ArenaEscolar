<?php
session_start();

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'representante' ||
    !isset($_SESSION['id_sala'])
) {
    header('Location: Login.php');
    exit();
} 

header('Location: ../Representante/aluno.php');
exit();
?>
