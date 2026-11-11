<?php

$host = '127.0.0.1';
$dbname = 'interclass';
$username = 'root'; // Altere se o seu usuário do banco for diferente
$password = '';     // Adicione a senha se houver configurado no seu servidor

try {
    // Criação da conexão PDO com charset utf8mb4 para evitar problemas de acentuação
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configura o modo de erro do PDO para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Opcional: define o modo de busca padrão para objetos ou arrays associativos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // echo "Conexão realizada com sucesso!";
} catch (PDOException $e) {
    // Em caso de erro, encerra a execução e exibe a mensagem de forma segura
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

?>