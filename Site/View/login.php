<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

</head>

<body>

    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>
        <input type="submit" value="Login">
        <a href="index.php">Crie uma conta</a>
    </form>

</body>

</html>

<?php
include_once '../Db/database.php';
include_once '../Controller/UsuarioController.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuarioController = new UsuarioController($pdo);
    $usuario = $usuarioController->login($email, $senha);

    if ($usuario && $usuario['tipo'] == 0) {
        session_start();
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        echo "<script>window.location.href='inicio.php';</script>";
    } elseif ($usuario && $usuario['tipo'] == 1) {
        session_start();
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        echo "<script>window.location.href='inicio_sala.php';</script>";
    } elseif ($usuario && $usuario['tipo'] == 2) {
        session_start();
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        echo "<script>window.location.href='inicio_admin.php';</script>";
    } else {
        echo "<script>window.alert('Email ou senha incorretos.');</script>";
        echo "<script>window.location.href='login.php';</script>";
    }
}
