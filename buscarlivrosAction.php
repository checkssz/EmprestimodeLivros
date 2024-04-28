<?php
// Inclui o arquivo de carregamento de variáveis de ambiente
require_once 'envLoader.php';

// Carrega as variáveis de ambiente do arquivo .env
loadEnv(__DIR__ . '/.env');

// Obtenha a consulta do usuário da URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Conexão com o banco de dados
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$dbname = getenv("DB_NAME");

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão com o banco de dados
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Consulta para filtrar livros com base na consulta do usuário
$sql = "SELECT codigo_do_livro, nome_do_livro, autor, descricao_do_livro
        FROM livro
        WHERE nome_do_livro LIKE ?";

$stmt = $conn->prepare($sql);
$consulta_param = '%' . $query . '%';
$stmt->bind_param('s', $consulta_param);
$stmt->execute();
$result = $stmt->get_result();

// Exibe os resultados
if ($result->num_rows > 0) {
    echo '<ul class="w3-ul w3-hoverable">';
    while ($livro = $result->fetch_assoc()) {
        echo '<li>';
        echo '<strong>' . $livro['nome_do_livro'] . '</strong> - ' . $livro['autor'] . '<br>';
        echo '<small>' . $livro['descricao_do_livro'] . '</small>';
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p class="w3-text-gray">Nenhum livro encontrado.</p>';
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
