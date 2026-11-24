
<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "parkcontrol";

$conexao = new mysqli(
    $servidor,
    $usuario,
    $senha,
    $banco
);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

date_default_timezone_set("America/Belem");

$conexao->query("SET time_zone = '-03:00'");

?>
