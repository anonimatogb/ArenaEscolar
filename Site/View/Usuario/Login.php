
<?php
session_start();

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rm = is_string($_POST['rm'] ?? null) ? trim($_POST['rm']) : '';
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
    $confirmacaoSenha = is_string($_POST['confirmacao_senha'] ?? null)
        ? $_POST['confirmacao_senha']
        : '';
   
    $usuarioController = new UsuarioController($pdo);

    $aluno = $usuarioController->buscarAlunoPorRM($rm);

    if ($aluno) {
        $_SESSION['id_usuario'] = $aluno['id'];
        $_SESSION['nome'] = $aluno['nome'];
        $_SESSION['rm'] = $aluno['rm'];
        $_SESSION['tipo'] = 'aluno';
        $_SESSION['id_sala'] = $aluno['id_sala'];

        header('Location: ../../../index.php');
        exit();
    } elseif ($senha !== '') {
        $usuario = $usuarioController->login($rm, $senha, $confirmacaoSenha);

        if ($usuario) {
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['rm'] = $usuario['rm'];
            $_SESSION['tipo'] = $usuario['tipo'];
            $_SESSION['id_sala'] = $usuario['id_sala'];

            header('Location: ../../../index.php');
            exit();
        } else {
            $erro = 'RM ou senha incorretos. No primeiro acesso, o representante deve informar e confirmar uma senha de pelo menos 8 caracteres.';
        }
    } else {
        $erro = 'Aluno não encontrado. Representantes devem informar RM e senha.';
    }
}  
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Usuario.css">
</head>

<body>

    <form action="Login.php" method="POST">
        <label for="rm">RM:</label>
        <input type="text" id="rm" name="rm" required><br><br>

        <label for="senha">Senha (somente representantes):</label>
        <input type="password" id="senha" name="senha"><br><br>

        <label for="confirmacao_senha">Confirme a senha no primeiro acesso de representante:</label>
        <input type="password" id="confirmacao_senha" name="confirmacao_senha"><br><br>

        <input type="submit" value="Entrar">
        <a href="../Administrador/login.php">Sou professor</a>
        <a href="../../../index.php">Voltar para a página inicial</a>
    </form>

    <?php if ($erro !== ''): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>  

</body>

</html>
