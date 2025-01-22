<?php
// Ativar exibição de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados
$servername = 'localhost';
$username = 'root'; // Ajuste se necessário
$password = '';     // Ajuste se necessário
$dbname = 'corretores';

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die('Falha na conexão: ' . $conn->connect_error);
}

// Verificar se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // Preparar e executar a exclusão do corretor
        $stmt = $conn->prepare('DELETE FROM corretores WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo 'Corretor deletado com sucesso!';
        } else {
            echo 'Erro ao deletar corretor: ' . $stmt->error;
        }

        // Fechar a conexão
        $stmt->close();
    } else {
        echo 'ID inválido.';
    }
} else {
    echo 'Requisição inválida.';
}

// Fechar a conexão
$conn->close();
?>
