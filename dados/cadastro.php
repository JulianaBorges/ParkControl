
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../conexao.php";

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
                VALUES (?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sss", $placa, $modelo, $cor);
        $stmt->execute();

        $id = $conexao->insert_id;

        $stmt->close();

        http_response_code(201);

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Veículo cadastrado com sucesso.",
            "id" => $id
        ]);
    }

    // CONSULTAR VEÍCULOS
    elseif ($metodo === "GET") {

        $id = $_GET["id"] ?? null;

        if ($id !== null && $id !== "") {

            if (!ctype_digit((string) $id) || (int) $id <= 0) {
                http_response_code(400);

                echo json_encode([
                    "sucesso" => false,
                    "mensagem" => "ID inválido."
                ]);
                exit;
            }

            $id = (int) $id;

            $stmt = $conexao->prepare(
                "SELECT * FROM veiculos WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();

            $resultado = $stmt->get_result();
            $veiculo = $resultado->fetch_assoc();

            $stmt->close();

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

            $resultado = $conexao->query(
                "SELECT * FROM veiculos ORDER BY id DESC"
            );

            $veiculos = $resultado->fetch_all(MYSQLI_ASSOC);

            echo json_encode($veiculos);
        }
    }

    // ATUALIZAR VEÍCULO
    elseif ($metodo === "PUT") {

        $dados = json_decode(file_get_contents("php://input"), true);

        $id = $dados["id"] ?? null;
        $placa = strtoupper(trim($dados["placa"] ?? ""));
        $modelo = trim($dados["modelo"] ?? "");
        $cor = trim($dados["cor"] ?? "");

        if (
            !is_scalar($id) ||
            !ctype_digit((string) $id) ||
            (int) $id <= 0 ||
            $placa === "" ||
            $modelo === "" ||
            $cor === ""
        ) {
            http_response_code(400);

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Informe todos os dados válidos do veículo."
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

        $id = (int) $id;

        $sql = "UPDATE veiculos
                SET placa = ?, modelo = ?, cor = ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sssi", $placa, $modelo, $cor, $id);
        $stmt->execute();

        $stmt->close();

        // Verifica se o veículo realmente existe
        $verifica = $conexao->prepare(
            "SELECT id FROM veiculos WHERE id = ?"
        );

        $verifica->bind_param("i", $id);
        $verifica->execute();

        $resultado = $verifica->get_result();
        $existe = $resultado->fetch_assoc();

        $verifica->close();

        if (!$existe) {
            http_response_code(404);

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Veículo não encontrado."
            ]);
            exit;
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

        if (
            !is_scalar($id) ||
            !ctype_digit((string) $id) ||
            (int) $id <= 0
        ) {
            http_response_code(400);

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Informe um ID válido."
            ]);
            exit;
        }

        $id = (int) $id;

        $stmt = $conexao->prepare(
            "DELETE FROM veiculos WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $excluido = $stmt->affected_rows;

        $stmt->close();

        if ($excluido === 0) {
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

} catch (mysqli_sql_exception $erro) {

    error_log($erro->getMessage());

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao executar operação no banco de dados."
    ]);

} catch (Throwable $erro) {

    error_log($erro->getMessage());

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro interno ao processar a solicitação."
    ]);
}
