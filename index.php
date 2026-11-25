
<?php
require_once "conexao.php";

$mensagem = "";
$erro = "";

// PROCESSAR FORMULÁRIOS
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $acao = $_POST["acao"] ?? "";

    try {

        if ($acao === "cadastrar" || $acao === "atualizar") {

            $placa = strtoupper(trim($_POST["placa"] ?? ""));
            $modelo = trim($_POST["modelo"] ?? "");
            $cor = trim($_POST["cor"] ?? "");

            if ($placa === "" || $modelo === "" || $cor === "") {
                throw new Exception("Preencha todos os campos.");
            }

            if (!preg_match('/^[A-Z0-9]{7}$/', $placa)) {
                throw new Exception("Placa inválida. Informe 7 caracteres.");
            }

            if ($acao === "cadastrar") {

                $stmt = $conexao->prepare(
                "INSERT INTO veiculos (placa, modelo, cor)
                 VALUES (?, ?, ?)"
                );

                $stmt->bind_param("sss", $placa, $modelo, $cor);

                $stmt->execute();

                $stmt->close();

                header("Location: index.php?status=cadastrado");
                exit;

            } else {

                $id = $_POST["id"] ?? null;

            $stmt = $conexao->prepare(
                    "UPDATE veiculos
                    SET placa = ?, modelo = ?, cor = ?
                    WHERE id = ?"
                );

                $stmt->bind_param("sssi", $placa, $modelo, $cor, $id);

                $stmt->execute();

                $stmt->close();

                header("Location: index.php?status=atualizado");
                exit;
            }
        }

        if ($acao === "excluir") {

            $id = $_POST["id"] ?? null;

           $stmt = $conexao->prepare(
                "DELETE FROM veiculos WHERE id = ?"
            );

            $id = (int) $_POST["id"];

            $stmt->bind_param("i", $id);
            $stmt->execute();

            $stmt->close();
        }

    } catch (PDOException $e) {
        $erro = "Não foi possível realizar a operação. Verifique os dados.";
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

// MENSAGENS
if (isset($_GET["status"])) {

    $mensagens = [
        "cadastrado" => "Veículo cadastrado com sucesso!",
        "atualizado" => "Veículo atualizado com sucesso!",
        "excluido" => "Veículo excluído com sucesso!"
    ];

    $mensagem = $mensagens[$_GET["status"]] ?? "";
}

// BUSCAR VEÍCULO PARA EDIÇÃO
$veiculoEdicao = null;

if (isset($_GET["editar"])) {

    $stmt = $conexao->prepare(
    "SELECT * FROM veiculos WHERE id = ?"
);

$id = (int) $_GET["editar"];

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$veiculoEdicao = $resultado->fetch_assoc();

$stmt->close();
}

// LISTAR VEÍCULOS
$resultado = $conexao->query(
    "SELECT * FROM veiculos ORDER BY id DESC"
);

$veiculos = $resultado->fetch_all(MYSQLI_ASSOC);

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ParkControl - Gerenciamento de Veículos</title>

    <link rel="stylesheet" href="./assets/css/style-cadastro.css">
</head>

<body>

<main class="container">

    <header>
        <h1>ParkControl</h1>
        <p class="subtitulo">Gerenciamento de Veículos</p>
    </header>

    <?php if ($mensagem): ?>
        <p class="mensagem"><?= escapar($mensagem) ?></p>
    <?php endif; ?>

    <?php if ($erro): ?>
        <p class="erro"><?= escapar($erro) ?></p>
    <?php endif; ?>

    <section class="cadastro">

        <h2>
            <?= $veiculoEdicao ? "Editar Veículo" : "Cadastrar Veículo" ?>
        </h2>

        <form method="POST" action="index.php">

            <input
                type="hidden"
                name="acao"
                value="<?= $veiculoEdicao ? "atualizar" : "cadastrar" ?>"
            >

            <?php if ($veiculoEdicao): ?>
                <input
                    type="hidden"
                    name="id"
                    value="<?= escapar($veiculoEdicao["id"]) ?>"
                >
            <?php endif; ?>

            <label for="placa">Placa:</label>
            <input
                type="text"
                id="placa"
                name="placa"
                maxlength="7"
                placeholder="Ex: ABC1D23"
                value="<?= escapar($veiculoEdicao["placa"] ?? "") ?>"
                required
            >

            <label for="modelo">Modelo:</label>
            <input
                type="text"
                id="modelo"
                name="modelo"
                placeholder="Ex: Toyota Corolla"
                value="<?= escapar($veiculoEdicao["modelo"] ?? "") ?>"
                required
            >

            <label for="cor">Cor:</label>
            <input
                type="text"
                id="cor"
                name="cor"
                placeholder="Ex: Prata"
                value="<?= escapar($veiculoEdicao["cor"] ?? "") ?>"
                required
            >

            <button type="submit">
                <?= $veiculoEdicao ? "Salvar Alterações" : "Cadastrar Veículo" ?>
            </button>

            <?php if ($veiculoEdicao): ?>
                <a class="cancelar" href="index.php">Cancelar edição</a>
            <?php endif; ?>

        </form>

    </section>

    <section class="lista">

        <h2>Veículos Cadastrados</h2>

        <div class="tabela-responsiva">
            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Modelo</th>
                        <th>Cor</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($veiculos) > 0): ?>

                        <?php foreach ($veiculos as $veiculo): ?>

                            <tr>
                                <td><?= escapar($veiculo["id"]) ?></td>
                                <td><?= escapar($veiculo["placa"]) ?></td>
                                <td><?= escapar($veiculo["modelo"]) ?></td>
                                <td><?= escapar($veiculo["cor"]) ?></td>

                                <td>
                                    <div class="acoes">

                                        <a
                                            class="btn-editar"
                                            href="index.php?editar=<?= escapar($veiculo["id"]) ?>"
                                        >
                                            Editar
                                        </a>

                                        <form method="POST" action="index.php">
                                            <input
                                                type="hidden"
                                                name="acao"
                                                value="excluir"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= escapar($veiculo["id"]) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn-excluir"
                                            >
                                                Excluir
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="vazio">
                                Nenhum veículo cadastrado.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>
        </div>

    </section>

</main>

</body>
</html>
