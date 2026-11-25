
<?php
require_once "../conexao.php";

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $placa = strtoupper(trim($_POST["placa"] ?? ""));
    $placa = str_replace([" ", "-"], "", $placa);

    if (empty($placa)) {
        $mensagem = "Informe a placa do veículo.";
        $tipoMensagem = "erro";
    } else {

        try {
            // 1. Verifica se o veículo está cadastrado
            $sql = "SELECT id FROM veiculos WHERE REPLACE(REPLACE(UPPER(placa), ' ', ''), '-', '') = :placa";

            $consulta = $conexao->prepare($sql);
            $consulta->execute(["placa" => $placa]);

            $veiculo = $consulta->fetch();

            if (!$veiculo) {
                $mensagem = "Veículo não cadastrado. Cadastre o veículo antes de registrar a entrada.";
                $tipoMensagem = "erro";

            } else {

                // 2. Verifica se já existe uma entrada em aberto
                $sql = "SELECT id
                        FROM movimentacoes
                        WHERE veiculo_id = :veiculo_id
                        AND data_saida IS NULL
                        LIMIT 1";

                $consulta = $conexao->prepare($sql);
                $consulta->execute([
                    "veiculo_id" => $veiculo["id"]
                ]);

                $movimentacao = $consulta->fetch();

                if ($movimentacao) {
                    $mensagem = "Este veículo já está estacionado.";
                    $tipoMensagem = "erro";

                } else {

                    // 3. Registra a entrada do veículo
                    // A data de entrada será preenchida automaticamente pelo MySQL
                    $sql = "INSERT INTO movimentacoes
                            (veiculo_id, status)
                            VALUES (:veiculo_id, 'ESTACIONADO')";

                    $consulta = $conexao->prepare($sql);
                    $consulta->execute([
                        "veiculo_id" => $veiculo["id"]
                    ]);

                    $mensagem = "Entrada do veículo registrada com sucesso!";
                    $tipoMensagem = "sucesso";
                }
            }

        } catch (PDOException $e) {
            $mensagem = "Ocorreu um erro ao registrar a entrada. Tente novamente.";
            $tipoMensagem = "erro";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Entrada - ParkControl</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 450px;
            margin: 70px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #263238;
        }

        label {
            display: block;
            margin-top: 25px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            text-transform: uppercase;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 13px;
            border: none;
            border-radius: 5px;
            background-color: #1976d2;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #125ca1;
        }

        .mensagem {
            margin-top: 20px;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }

        .sucesso {
            background-color: #d4edda;
            color: #155724;
        }

        .erro {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Registro de Entrada</h1>

        <form method="POST" action="">

            <label for="placa">Placa do veículo:</label>

            <input
                type="text"
                id="placa"
                name="placa"
                placeholder="Ex.: ABC1D23"
                maxlength="10"
                required
                autofocus
            >

            <button type="submit">
                Registrar entrada
            </button>

        </form>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo $tipoMensagem; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
