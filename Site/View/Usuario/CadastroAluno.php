<?php
include_once '../../../Db/database.php';
include_once '../../Controller/UsuarioController.php';
include_once '../../Controller/SalaController.php';

$salaController = new SalaController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $rm = $_POST['rm'];
    $senha = $_POST['senha'];
    $id_sala = $_POST['id_sala']; 
 

    $usuarioController = new UsuarioController($pdo);
    $resultado = $usuarioController->cadastrarAluno($nome, $rm, $senha, $id_sala);

    if ($resultado) {
        echo "<script>window.alert('Aluno cadastrado com sucesso!');</script>";
        echo "<script>window.location.href='login.php';</script>";                  
    } else {
        echo "<script>window.alert('Rm/Ra já cadastrado ou erro ao cadastrar aluno.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Aluno</title>
</head>
<body>


<form action="CadastroAluno.php" method="POST">

<label for="nome">Nome:</label>
<input type="text" id="nome" name="nome" required><br><br>

<label for="rm">RM/RA:</label>
<input type="text" id="rm" name="rm" required><br><br>

<label for="senha">Senha:</label>
<input type="password" id="senha" name="senha" required><br><br>

<label for="id_sala">Sala:</label>
<select name="id_sala" id="id_sala" required>
    <?php  
    $salas = $salaController->listarSalas();
    echo "<option value=''>Selecione uma sala</option>";
    foreach ($salas as $sala) {
        if ($sala['id'] == 1) {
            continue; // Pula a sala com ID 1
        }
        echo "<option value='{$sala['id']}'>{$sala['nome']}</option>";
    }
    
    ?>
</select>
<br><br>

<input type="submit" value="Cadastrar">
<a href="login.php">Já tem uma conta? Faça login aqui.</a>
</form>


</body>
</html>

