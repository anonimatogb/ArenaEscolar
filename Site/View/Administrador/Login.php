<?php
session_start();

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Model/AdministradorModel.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeUsuario = trim($_POST['nome_usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $administrador = (new AdministradorModel($pdo))->login($nomeUsuario, $senha);

    if ($administrador) {
        session_regenerate_id(true);
        $_SESSION['id_administrador'] = $administrador['id'];
        $_SESSION['nome'] = $administrador['nome'];
        $_SESSION['tipo'] = 'ADM';
        unset($_SESSION['id_usuario'], $_SESSION['id_sala'], $_SESSION['rm']);
        header('Location: ../../../index.php');
        exit();
    }

    $erro = 'Usuário ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar como professor</title>
</head>
<body>
    <h1>Entrar como professor</h1>
    <form action="Login.php" method="POST">
        <label for="nome_usuario">Usuário:</label>
        <input type="text" id="nome_usuario" name="nome_usuario" required>
        <br><br>
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        <br><br>
        <button type="submit">Entrar</button>
        <a href="../Usuario/Login.php">Sou aluno</a>
    </form>
    <?php if ($erro !== ''): ?>
        <p><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</body>
</html>
