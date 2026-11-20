<?php
session_start();
require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';

if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nome'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($nome) && !empty($senha)) {
        $usuarioController = new UsuarioController($pdo);
        $resultado = $usuarioController->cadastrarAdministrador($nome, $senha);

        if ($resultado) {
            echo "<p>Administrador cadastrado com sucesso!</p>";
        } else {
            echo "<p>Erro ao cadastrar administrador. Verifique se o nome de usuário já existe.</p>";
        }
    } else {
        echo "<p>Por favor, preencha todos os campos.</p>";
    }
}


?>
<!DOCTYPE html>   
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de administrador</title>
</head>
<body>

    <p><a href="cadastroAdministrador.php">Entrar como professor</a></p>

        <form action="" method="POST">
            <label for="nome">Nome de usuário:</label>
            <input type="text" id="nome" name="nome" maxlength="255" required>
            <br><br>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <br><br>

            <button type="submit">Cadastrar administrador</button>
        </form>

</body>
</html>
