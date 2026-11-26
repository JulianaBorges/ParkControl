
<?php

// Conectar ao banco de dados
require_once "../conexao.php";

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acesso inválido.");
}

// Receber o ID da movimentação
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

// Verificar se o ID é válido
if (!$id) {
    die("Movimentação inválida.");
}

// Atualizar a movimentação no banco
$sql = "UPDATE movimentacoes
        SET data_saida = NOW(),
            status = 'finalizada'
        WHERE id = ?
        AND status = 'estacionado'
        AND data_saida IS NULL";

// Preparar consulta
$stmt = $conexao->prepare($sql);

// Informar o ID
$stmt->bind_param("i", $id);

// Executar atualização
$stmt->execute();

// Verificar se a saída foi registrada
if ($stmt->affected_rows == 1) {

    echo "<h1>Saída registrada com sucesso!</h1>";

    echo "<p>O veículo foi liberado do estacionamento.</p>";

} else {

    echo "<h1>Não foi possível registrar a saída.</h1>";

    echo "<p>Verifique se o veículo ainda está estacionado.</p>";

}

echo '<a href="../index.php">Voltar ao início</a>';

?>
