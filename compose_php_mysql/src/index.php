<?php
$host = 'mysql';
$user = 'meu_usuario';
$pass = 'minha_senha';
$db = 'meu_banco';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

echo "Conectado ao MySQL com sucesso!";
?>
