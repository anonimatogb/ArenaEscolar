
<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: Login.php');
    exit();
}
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'aluno') {
    header('Location: Login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Usuario.css">
</head>
<body>
    <h1>Bem-vindo ao Arena Escolar!</h1>
<nav>
        <ul>
            <p>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>!</p>
            <li><a href="inicio.php">Início</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="../../../index.php">Voltar para a página inicial</a></li>
            <li><a href="sair.php">Sair</a></li>
        </ul>
    </nav>

</body>
</html>

