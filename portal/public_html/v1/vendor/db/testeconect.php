<?php
$host = localhost;//"127.0.0.1"; // ou "localhost"
$user = "suporte";
$pass = "123"; // ou sua senha
$dbname = "refsoft";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
echo "Conexão bem-sucedida!";
$conn->close();
?>
