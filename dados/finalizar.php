
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

// Buscar a data de entrada
$sql = "SELECT data_entrada
        FROM movimentacoes
        WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$movimentacao = $resultado->fetch_assoc();

if (!$movimentacao) {
    die("Movimentação não encontrada.");
}

// Calcular tempo de permanência em minutos
$entrada = new DateTime($movimentacao['data_entrada']);
$saida = new DateTime();

$intervalo = $entrada->diff($saida);

$tempoMinutos =
    ($intervalo->days * 24 * 60) +
    ($intervalo->h * 60) +
    $intervalo->i;


// Atualizar a movimentação no banco
$sql = "UPDATE movimentacoes
        SET
            data_saida = NOW(),
            status = 'FINALIZADO',
            tempo_permanencia_minutos = ?
        WHERE id = ?
        AND data_saida IS NULL";

// Preparar consulta
$stmt = $conexao->prepare($sql);

// Informar o ID
$stmt->bind_param(
    "ii",
    $tempoMinutos,
    $id
);
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

echo '<a href="index.php">Voltar ao início</a>';

?>
