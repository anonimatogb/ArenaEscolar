<?php

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Time</title>
</head>
<body>
    <h1>Cadastro de Time</h1>
    <form action="processar_cadastro_time.php" method="post">
        <label for="nome">Nome do Time:</label>
        <input type="text" id="nome" name="nome" required>
        <br><br>
        <label for="alunos">Alunos:</label>
        <textarea id="alunos" name="alunos" required></textarea>
        <br><br>
        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>