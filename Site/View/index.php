
<?php
session_start();

require_once __DIR__ . '/../Db/Database.php';
require_once __DIR__ . '/../Controller/UsuarioController.php';

$usuarioController = new UsuarioController();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $rm = trim($_POST['rm']);
    $senha = $_POST['senha'];
    $codigo = trim($_POST['codigo']);

    if (empty($nome) || empty($rm) || empty($senha) || empty($codigo)) {

        $erro = 'Preencha todos os campos.';

    } else {

        // A validação do código e a identificação da sala
        // serão implementadas quando criarmos o banco de dados.

        $erro = 'O cadastro será liberado após a configuração das salas e dos códigos de autorização.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Representante</title>
</head>
<body>

<h2>Cadastro de Representante</h2>

<?php if (!empty($erro)) { ?>
    <p><?php echo htmlspecialchars($erro); ?></p>
<?php } ?>

<form action="index.php" method="POST">

    <label for="nome">Nome completo:</label>
    <input type="text" id="nome" name="nome" required>

    <br><br>

    <label for="rm">RM:</label>
    <input type="text" id="rm" name="rm" required>

    <br><br>

    <label for="senha">Senha:</label>
    <input type="password" id="senha" name="senha" required>

    <br><br>

    <label for="codigo">Código de autorização da sala:</label>
    <input type="password" id="codigo" name="codigo" required>

    <br><br>

    <input type="submit" value="Cadastrar representante">

    <br><br>

    <a href="login.php">Já tem uma conta? Faça login</a>

</form>

</body>
</html>
