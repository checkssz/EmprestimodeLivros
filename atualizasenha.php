<?php
// Inclui o arquivo de carregamento de variáveis de ambiente
require_once 'envLoader.php';

// Carrega as variáveis de ambiente do arquivo .env
loadEnv(__DIR__ . '/.env');

// Conexão com o banco de dados
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$dbname = getenv("DB_NAME");

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Recebe o ID do usuário e a senha clara do formulário (ou defina manualmente para testar)
$usuario_id = $_POST['id'] ?? 1; // Substitua 1 pelo ID do usuário desejado
$senha_clara = $_POST['senhaclara'] ?? 'senha123'; // Substitua 'nova_senha' pela senha desejada

// Criptografa a nova senha
$senha_criptografada = password_hash($senha_clara, PASSWORD_BCRYPT);

// Atualiza a senha do usuário no banco de dados
$sql = "UPDATE usuario SET senha = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $senha_criptografada, $usuario_id);
$stmt->execute();

// Verifica se a atualização foi bem-sucedida
if ($stmt->affected_rows > 0) {
    echo "Senha atualizada com sucesso para o usuário ID $usuario_id";
} else {
    echo "Erro ao atualizar a senha ou nenhum registro atualizado";
}

// Fecha a consulta preparada e a conexão com o banco de dados
$stmt->close();
$conn->close();
?>
