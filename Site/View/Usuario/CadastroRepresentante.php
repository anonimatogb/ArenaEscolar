
<?php
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: ../Administrador/Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';
require_once __DIR__ . '/../../Controller/SalaController.php';

$salaController = new SalaController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $rm = $_POST['rm'];
    $id_sala = $_POST['id_sala'];
    $genero = $_POST['genero'];
    $senha = $_POST['senha'] ?? '';

    $usuarioController = new UsuarioController($pdo);
    $resultado = $usuarioController->cadastrarRepresentante($nome, $rm, $id_sala, $genero, $senha);

    if ($resultado) {
        echo "<script>window.alert('Representante cadastrado com sucesso!');</script>";
        echo "<script>window.location.href='../Administrador/inicio.php';</script>";
    } else {
        echo "<script>window.alert('Não foi possível cadastrar. Verifique se o RM já existe ou se a sala já possui dois representantes.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Representante</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Usuario.css">
</head>

<body>

    <h1>Cadastro de Representante</h1>

    <form action="CadastroRepresentante.php" method="POST">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="rm">RM:</label> 
        <input type="text" id="rm" name="rm" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" minlength="8" required><br><br>

        <label for="id_sala">Sala:</label>
        <select name="id_sala" id="id_sala" required>
            <option value="">Selecione uma sala</option>


            <?php  
            $salas = $salaController->listarSalas();

            foreach ($salas as $sala) {
                echo "<option value='{$sala['id']}'>{$sala['nome']}</option>";
            }
            ?>
        </select>

        <br><br>
    
        <label for="genero">Gênero:</label>
        <select id="genero" name="genero" required>
            <option value="masculino">Masculino</option>
            <option value="feminino">Feminino</option>
        </select>

        <br><br>

        <input type="submit" value="Cadastrar">

        <a href="../Administrador/inicio.php">Voltar</a>

    </form>

</body>

</html>
