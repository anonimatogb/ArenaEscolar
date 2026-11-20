<?php
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: Login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do professor</title>
</head>
<body>
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8') ?>!</h1>
    <h2>Painel do professor</h2>
    <ul>
        <li><a href="../../../index.php">Início</a></li>
        <li><a href="../Usuario/CadastroRepresentante.php">Cadastrar representante</a></li>
             <li><a href="CadastroAdministrador.php">Cadastrar professor</a></li>
        <li><a href="jogos.php">Selecionar salas e agendar confrontos</a></li>
        <li><a href="../Modalidade/modalidade.php">Cadastrar e consultar modalidades</a></li>
        <li><a href="../Sala/CadastroSala.php">Consultar salas</a></li>
        <li><a href="../../../index.php">Voltar para a página inicial</a></li>
        <li><a href="../Usuario/sair.php">Sair</a></li>
    </ul>
</body>
</html>
