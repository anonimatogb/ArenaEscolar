<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Sala</title>
</head>
<body>


<form action="CadastroSala.php" method="POST">

<label for="nome">Nome:</label>
<input type="text" id="nome" name="nome" required><br><br>

<input type="submit" value="Cadastrar">
<a href="../admin/inicioadmin.php">Voltar</a>
</form>


</body>
</html>

<?php
include_once '../../../Db/database.php';
include_once '../../Controller/SalaController.php';


session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];

 

    $salaController = new SalaController($pdo);
    $resultado = $salaController->cadastrarSala($nome);

    if ($resultado) {
        echo "<script>window.alert('Sala cadastrada com sucesso!');</script>";                
    } else {
        echo "<script>window.alert('Erro ao cadastrar sala.');</script>";
    }
}
?>