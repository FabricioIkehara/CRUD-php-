<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "corretores";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualizar os dados do corretor
    $id = htmlspecialchars($_POST['id']);
    $name = htmlspecialchars($_POST['editNome']);
    $creci = htmlspecialchars($_POST['editCreci']);
    $cpf = htmlspecialchars($_POST['editCpf']);

    // Validar os dados
    if (strlen($name) < 3 || strlen($creci) < 3 || !preg_match("/^\d{11}$/", $cpf)) {
        echo "Dados inválidos.";
    } else {
        $stmt = $conn->prepare("UPDATE corretores SET name = ?, creci = ?, cpf = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $creci, $cpf, $id);

        if ($stmt->execute()) {
            echo "Dados atualizados com sucesso.";
        } else {
            echo "Erro ao atualizar dados: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    // Selecionar todos os corretores
    $sql = "SELECT id, name, creci, cpf FROM corretores";
    $result = $conn->query($sql);

    $corretores = [];

    // Verificar se há resultados
    if ($result->num_rows > 0) {
        // Adicionar dados ao array
        while ($row = $result->fetch_assoc()) {
            $corretores[] = $row;
        }
    } else {
        echo "Nenhum corretor cadastrado.";
    }

    // Fechar a conexão
    $conn->close();

    // Retornar os dados em formato JSON
    header('Content-Type: application/json');
    echo json_encode($corretores);
}
?>
