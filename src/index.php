<?php
$host = 'mysql';
$user = 'murilo';
$pass = '190906';
$db = 'my_thermometer';

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error){
   die("Erro de conexão:" .$conn->connect_error);
}


echo"Conectado ao MySQL com sucesso, pq sou bom!"
?>