<?php

$host = "localhost";
$porta = "3306";
$banco = "estacionamento";
$usuario = "root";
$senha = "Suamaeeminha@12";

try {
    $pdo = new PDO("mysql:host=$host;port=$porta;dbname=$banco;chartset=utf8mb4", $usuario, $senha);
     $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
     );
     echo "Conexão com o banco realizada com sucesso!";

} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}