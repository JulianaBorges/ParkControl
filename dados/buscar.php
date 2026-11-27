
<?php

// Conectar ao banco de dados
require_once __DIR__ . "/../conexao.php";

// Receber a placa digitada
$placa = strtoupper(trim($_GET["placa"] ?? ""));

// Inicializar variável
$veiculo = null;

// Verificar se a placa foi informada
if ($placa === "") {
    die("Informe uma placa.");
}

// Procurar veículo estacionado no banco
$sql = "SELECT
            m.id,
            v.placa,
            v.modelo,
            v.cor,
            m.data_entrada
        FROM movimentacoes m
        INNER JOIN veiculos v
            ON m.veiculo_id = v.id
        WHERE v.placa = ?
            AND m.status = 'estacionado'
            AND m.data_saida IS NULL
        ORDER BY m.id DESC
        LIMIT 1";

// Preparar consulta
$stmt = $conexao->prepare($sql);

// Enviar a placa para a consulta
$stmt->bind_param("s", $placa);

// Executar pesquisa
$stmt->execute();

// Receber resultado
$resultado = $stmt->get_result();

// Guardar os dados do veículo
$veiculo = $resultado->fetch_assoc();

// Fechar consulta
$stmt->close();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resultado da Pesquisa</title>

    <link rel="stylesheet" href="../assets/css/style-saida.css">
</head>

<body>

    <main class="container">

        <h1>ParkControl</h1>

        <h2>Resultado da Pesquisa</h2>

        <?php if ($veiculo): ?>

            <h3>Veículo encontrado!</h3>

            <p>
                <strong>Placa:</strong>
                <?= htmlspecialchars($veiculo["placa"], ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Modelo:</strong>
                <?= htmlspecialchars($veiculo["modelo"], ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Cor:</strong>
                <?= htmlspecialchars($veiculo["cor"], ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Horário de entrada:</strong>
                <?= htmlspecialchars($veiculo["data_entrada"], ENT_QUOTES, "UTF-8") ?>
            </p>

            <form action="finalizar.php" method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $veiculo["id"] ?>"
                >

                <button type="submit">
                    Confirmar saída
                </button>

            </form>

        <?php else: ?>

            <p class="erro">
                Veículo não encontrado ou não está estacionado.
            </p>

        <?php endif; ?>

        <a href="../pages/saida.php">Voltar</a>

    </main>

</body>

</html>
