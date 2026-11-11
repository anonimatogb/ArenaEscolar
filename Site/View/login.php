
<?php
session_start();

require_once __DIR__ . '/../Controller/UsuarioController.php';

$usuarioController = new UsuarioController();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rm = trim($_POST['rm']);
    $senha = $_POST['senha'];

    if (empty($rm) || empty($senha)) {

        $erro = 'Preencha o RM e a senha.';

    } else {

        $usuario = $usuarioController->login($rm, $senha);

        if ($usuario) {

            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['rm'] = $usuario['rm'];
            $_SESSION['tipo'] = $usuario['tipo'];
            $_SESSION['sala_id'] = $usuario['sala_id'] ?? null;

            if ($usuario['tipo'] == 0) {

                header('Location: inicio.php');
                exit();

            } elseif ($usuario['tipo'] == 1) {

                header('Location: inicio_sala.php');
                exit();

            } elseif ($usuario['tipo'] == 2) {

                header('Location: inicio_admin.php');
                exit();

            } else {

                session_destroy();
                $erro = 'Tipo de usuário inválido.';
            }

        } else {

            $erro = 'RM ou senha incorretos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Arena Escolar</title>
</head>

<body>

    <h2>Login - Arena Escolar</h2>

    <?php if (!empty($erro)) { ?>
        <p><?php echo htmlspecialchars($erro); ?></p>
    <?php } ?>

    <form action="login.php" method="POST">

        <label for="rm">RM:</label>
        <input type="text" id="rm" name="rm" required>

        <br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <br><br>

        <input type="submit" value="Entrar">

        <br><br>

        <a href="index.php">Cadastro de representante</a>

    </form>

</body>

</html>
