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
        <?php if (empty($_SESSION['id_usuario'])): ?>
            <li><a href="Site/View/Usuario/Login.php">Entrar</a></li>
            <li><a href="Site/View/Usuario/CadastroRepresentante.php">Cadastrar representante</a></li>
        <?php elseif ($tipo === 'representante'): ?>
            <li><a href="Site/View/Representante/inicio.php">Painel do representante</a></li>
            <li><a href="Site/View/Representante/aluno.php">Cadastrar e consultar alunos da sala</a></li>
        <?php elseif ($tipo === 'aluno'): ?>
            <li><a href="Site/View/Usuario/inicio.php">Minha página</a></li>
        <?php endif; ?>
    </ul>

    <h2>Funcionalidades</h2>
    <ul>
        <li><a href="Site/View/Modalidade/modalidade.php">Cadastrar e consultar modalidades</a></li>
        <li><a href="Site/View/Sala/CadastroSala.php">Consultar salas</a></li>
        <li><a href="Site/Controller/CadastroAdministradorController.php">Cadastrar administrador</a></li>
    </ul>
</body>
</html>
