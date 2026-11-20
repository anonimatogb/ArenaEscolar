<?php
session_start();

$tipo = $_SESSION['tipo'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arena Escolar</title>
</head>
<body>
    <h1>Arena Escolar</h1>

    <?php if (isset($_SESSION['nome'])): ?>
        <p>Bem-vindo, <?= htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8') ?>.</p>
    <?php endif; ?>

     
    <h2>Acesso</h2>
    <ul>
        <?php if (empty($_SESSION['id_usuario']) && empty($_SESSION['id_administrador'])): ?>
            <li><a href="Site/View/Administrador/Login.php">Entrar como professor</a></li>
            <li><a href="Site/View/Usuario/Login.php">Entrar como representante ou aluno</a></li>
            <?php elseif ($tipo === 'ADM' && isset($_SESSION['id_administrador'])): ?>
                <li><a href="Site/View/Administrador/inicio.php">Painel do professor</a></li>
        <?php elseif ($tipo === 'representante'): ?>
            <li><a href="Site/View/Representante/inicio.php">Painel do representante</a></li>
        <?php elseif ($tipo === 'aluno'): ?>
            <li><a href="Site/View/Usuario/inicio.php">Minha página</a></li>
        <?php endif; ?>
    </ul>
    <ul>
<h2>
    Jogos
</h2>



    </ul>
</body>
</html>
