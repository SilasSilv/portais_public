<?php
$servername = "localhost";
$username = "root";
$password = "";

// Cria a conexão com o banco de dados
$conn = mysqli_connect($servername, $username, $password);

// Verifica se a conexão foi bem sucedida
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}
echo "Conexão realizada com sucesso";
?>
