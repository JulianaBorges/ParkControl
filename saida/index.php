
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ParkControl - Registro de Saída</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="container">

        <h1>ParkControl</h1>

        <h2>Registro de Saída</h2>

        <p>Informe a placa do veículo para registrar sua saída.</p>

        <form action="buscar.php" method="GET">

            <label for="placa">Placa do veículo:</label>

            <input
                type="text"
                id="placa"
                name="placa"
                placeholder="Ex: ABC1D23"
                maxlength="10"
                required
            >

            <button type="submit">
                Pesquisar veículo
            </button>

        </form>

    </main>

</body>

</html>
