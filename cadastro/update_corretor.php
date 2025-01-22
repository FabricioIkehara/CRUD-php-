<?php
// Ativar exibição de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados
$servername = 'localhost';
$username = 'root'; // Ajuste se necessário
$password = '';     // Ajuste se necessário
$dbname = 'corretores';

// Verificar se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber os dados do formulário
    $id = isset($_POST['editId']) ? htmlspecialchars($_POST['editId']) : null;
    $name = isset($_POST['editName']) ? htmlspecialchars($_POST['editName']) : null;
    $creci = isset($_POST['editCreci']) ? htmlspecialchars($_POST['editCreci']) : null;
    $cpf = isset($_POST['editCpf']) ? htmlspecialchars($_POST['editCpf']) : null;

    // Log dos dados recebidos
    error_log("Dados recebidos: ID=$id, Nome=$name, CRECI=$creci, CPF=$cpf");

    // Verificar se os campos estão recebendo valores
    if ($id === null || $name === null || $creci === null || $cpf === null) {
        die("Erro: Dados do formulário não recebidos corretamente.");
    }

    // Validar os dados
    $errors = [];
    if (strlen($name) < 3) {
        $errors[] = "Nome precisa ter mais de 2 caracteres";
    }
    if (strlen($creci) < 3) {
        $errors[] = "CRECI precisa ter mais de 2 caracteres";
    }
    if (!preg_match("/^\d{11}$/", $cpf)) {
        $errors[] = "CPF precisa ter 11 caracteres numéricos";
    }

    // Se houver erros, exibir mensagens de erro
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        exit(); // Parar a execução se houver erros
    } else {
        // Conectar ao banco de dados
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar a conexão
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        // Preparar e executar a atualização dos dados
        $stmt = $conn->prepare("UPDATE corretores SET name = ?, creci = ?, cpf = ? WHERE id = ?");

        // Verificar se a preparação foi bem-sucedida
        if ($stmt === false) {
            die("Erro ao preparar a instrução SQL: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("sssi", $name, $creci, $cpf, $id);

        // Log antes de executar a query
        error_log("Executando SQL: UPDATE corretores SET name='$name', creci='$creci', cpf='$cpf' WHERE id=$id");

        if ($stmt->execute()) {
            echo "Dados atualizados com sucesso!";
        } else {
            echo "Erro ao atualizar dados: " . $stmt->error;
        }

        // Fechar a conexão
        $stmt->close();
        $conn->close();
    }
} else {
    echo "Formulário não enviado corretamente.";
}
?>
