
<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'representante') {
    echo "<script>window.alert('Acesso permitido somente para representantes.');</script>";
    echo "<script>window.location.href='../Usuario/Login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">  

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Representante</title>
</head>

<body>

    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['nome']) ?>!</h1>

    <h2>Painel do Representante</h2>

    <p>Gerenciamento da sua sala.</p>

    <p><a href="aluno.php">Cadastrar e consultar alunos da minha sala</a></p>

</body>

</html>
