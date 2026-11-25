
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

$metodo = $_SERVER["REQUEST_METHOD"];

try {

    // CADASTRAR VEÍCULO
    if ($metodo === "POST") {

        $dados = json_decode(file_get_contents("php://input"), true);

        $placa = strtoupper(trim($dados["placa"] ?? ""));
        $modelo = trim($dados["modelo"] ?? "");
        $cor = trim($dados["cor"] ?? "");

        if ($placa === "" || $modelo === "" || $cor === "") {
            http_response_code(400);
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Preencha todos os campos."
            ]);
            exit;
        }

        if (!preg_match('/^[A-Z0-9]{7}$/', $placa)) {
            http_response_code(400);
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Placa inválida."
            ]);
            exit;
        }

        $sql = "INSERT INTO veiculos (placa, modelo, cor)
                VALUES (:placa, :modelo, :cor)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":placa" => $placa,
            ":modelo" => $modelo,
            ":cor" => $cor
        ]);

        http_response_code(201);

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Veículo cadastrado com sucesso.",
            "id" => $pdo->lastInsertId()
        ]);

    }

    // CONSULTAR VEÍCULOS
    elseif ($metodo === "GET") {

        $id = $_GET["id"] ?? null;

        if ($id) {

            $stmt = $pdo->prepare(
                "SELECT * FROM veiculos WHERE id = :id"
            );

            $stmt->execute([":id" => $id]);

            $veiculo = $stmt->fetch();

            if (!$veiculo) {
                http_response_code(404);
                echo json_encode([
                    "sucesso" => false,
                    "mensagem" => "Veículo não encontrado."
                ]);
                exit;
            }

            echo json_encode($veiculo);

        } else {

            $stmt = $pdo->query("SELECT * FROM veiculos ORDER BY id DESC");

            echo json_encode($stmt->fetchAll());

        }

    }

    // ATUALIZAR VEÍCULO
    elseif ($metodo === "PUT") {

        $dados = json_decode(file_get_contents("php://input"), true);

        $id = $dados["id"] ?? null;
        $placa = strtoupper(trim($dados["placa"] ?? ""));
        $modelo = trim($dados["modelo"] ?? "");
        $cor = trim($dados["cor"] ?? "");

        if (!$id || !$placa || !$modelo || !$cor) {
            http_response_code(400);
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Informe todos os dados do veículo."
            ]);
            exit;
        }

        $sql = "UPDATE veiculos
                SET placa = :placa,
                    modelo = :modelo,
                    cor = :cor
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":placa" => $placa,
            ":modelo" => $modelo,
            ":cor" => $cor,
            ":id" => $id
        ]);

        if ($stmt->rowCount() === 0) {
            $verifica = $pdo->prepare("SELECT id FROM veiculos WHERE id = ?");
            $verifica->execute([$id]);

            if (!$verifica->fetch()) {
                http_response_code(404);
                echo json_encode([
                    "sucesso" => false,
                    "mensagem" => "Veículo não encontrado."
                ]);
                exit;
            }
        }

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Veículo atualizado com sucesso."
        ]);

    }

    // EXCLUIR VEÍCULO
    elseif ($metodo === "DELETE") {

        $dados = json_decode(file_get_contents("php://input"), true);

        $id = $dados["id"] ?? $_GET["id"] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Informe o ID do veículo."
            ]);
            exit;
        }

        $stmt = $pdo->prepare(
            "DELETE FROM veiculos WHERE id = :id"
        );

        $stmt->execute([":id" => $id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Veículo não encontrado."
            ]);
            exit;
        }

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Veículo excluído com sucesso."
        ]);

    }

    else {

        http_response_code(405);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Método não permitido."
        ]);

    }

} catch (PDOException $erro) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao executar operação no banco de dados."
    ]);

}
