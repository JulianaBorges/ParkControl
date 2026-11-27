<?php
$host = "127.0.0.1:3308";
$banco = "parkcontrol";
$usuario = "root";
$senha = ""; // Replace with your actual password

$conexao = new mysqli(
   $host,
    $usuario,
    $senha,
    $banco,
    //$porta
);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

date_default_timezone_set("America/Belem");

$conexao->query("SET time_zone = '-03:00'");

?>