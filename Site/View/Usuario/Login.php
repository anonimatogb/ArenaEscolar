<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>

    <form action="login.php" method="POST">
        <label for="rm">RM:</label>
        <input type="text" id="rm" name="rm" required><br><br>
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>
        <input type="submit" value="Login">
        <a href="CadastroAluno.php">Crie uma conta</a>
    </form>

</body>

</html>

<?php
include_once '../../../Db/database.php';
include_once '../../Controller/UsuarioController.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rm = $_POST['rm'];
    $senha = $_POST['senha'];

    $usuarioController = new UsuarioController($pdo);
    $usuario = $usuarioController->login($rm, $senha);
    
    if ($usuario['tipo'] === 'aluno' || $usuario['tipo'] === 'rep') {
        session_start();
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['rm'] = $usuario['rm'];
        echo "<script>window.alert('Login realizado com sucesso!');</script>";
        echo "<script>window.location.href='inicio.php';</script>";
    } elseif ($usuario['tipo'] === 'admin') {
        session_start();
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['rm'] = $usuario['rm'];
        echo "<script>window.alert('Login realizado com sucesso!');</script>";
        echo "<script>window.location.href='inicioadmin.php';</script>";
    }  
    else {
        echo "<script>window.alert('RM ou senha incorretos.');</script>";
    }
}