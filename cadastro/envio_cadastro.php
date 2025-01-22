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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados do formulário
    $nome = isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : null;
    $creci = isset($_POST['creci']) ? htmlspecialchars($_POST['creci']) : null;
    $cpf = isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : null;

    // Verificar se os campos estão recebendo valores
    if ($nome === null || $creci === null || $cpf === null) {
        die('Erro: Dados do formulário não recebidos corretamente.');
    }

    // Validar os dados
    $errors = [];
    if (strlen($nome) < 3) {
        $errors[] = 'Nome precisa ter mais de 2 caracteres';
    }
    if (strlen($creci) < 3) {
        $errors[] = 'CRECI precisa ter mais de 2 caracteres';
    }
    if (!preg_match('/^\d{11}$/', $cpf)) {
        $errors[] = 'CPF precisa ter 11 caracteres numéricos';
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
            die('Falha na conexão: ' . $conn->connect_error);
        }

        // Preparar e executar a inserção dos dados
        $stmt = $conn->prepare('INSERT INTO corretores (name, creci, cpf) VALUES (?, ?, ?)');

        // Verificar se a preparação foi bem-sucedida
        if ($stmt === false) {
            die('Erro ao preparar a instrução SQL: ' . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param('sss', $nome, $creci, $cpf);

        if ($stmt->execute()) {
            echo 'Cadastro realizado com sucesso!';
        } else {
            echo 'Erro ao cadastrar: ' . $stmt->error;
        }

        // Fechar a conexão
        $stmt->close();
        $conn->close();
    }
} else {
    echo 'Formulário não enviado corretamente.';
}
?>
