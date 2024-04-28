<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão
session_start();

// Inclua o carregamento de variáveis de ambiente
require_once 'envLoader.php';

// Carregue as variáveis de ambiente do arquivo .env
loadEnv(__DIR__ . '/.env');

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

// Obtenha o ID do usuário logado
$usuario_id_dono = $_SESSION['id'];

// Consulta para obter solicitações de empréstimo pendentes
$sql_solicitacoes_pendentes = "SELECT e.id_emprestimo, l.nome_do_livro, u.nome_completo AS nome_solicitante, e.data_de_inicio_de_emprestimo, e.data_de_fim_de_emprestimo
                              FROM emprestimo e
                              JOIN Livro l ON e.codigo_do_livro = l.codigo_do_livro
                              JOIN usuario u ON e.usuario_id_dono = u.id
                              WHERE l.usuario_id_dono = ? AND e.status = 'averiguando'";

$stmt_solicitacoes_pendentes = $conn->prepare($sql_solicitacoes_pendentes);
$stmt_solicitacoes_pendentes->bind_param('i', $usuario_id_dono);
$stmt_solicitacoes_pendentes->execute();
$result_solicitacoes_pendentes = $stmt_solicitacoes_pendentes->get_result();

// Exibir solicitações pendentes com estilização do W3.CSS
echo "<h2 class='w3-text-teal w3-padding'>Solicitações de Empréstimo Pendentes</h2>";
if ($result_solicitacoes_pendentes->num_rows > 0) {
    echo "<ul class='w3-ul w3-card-4 w3-margin'>";
    while ($row = $result_solicitacoes_pendentes->fetch_assoc()) {
        echo "<li class='w3-padding'>";
        echo "<strong>{$row['nome_do_livro']}</strong> - Solicitante: {$row['nome_solicitante']} | Início: {$row['data_de_inicio_de_emprestimo']} | Fim: {$row['data_de_fim_de_emprestimo']}";
        echo " <a href='aceitar.php?id={$row['id_emprestimo']}' class='w3-button w3-green w3-margin-left'>Aceitar</a>";
        echo " <a href='recusar.php?id={$row['id_emprestimo']}' class='w3-button w3-red w3-margin-left'>Recusar</a>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='w3-text-grey w3-padding w3-center'>Não há solicitações pendentes no momento.</p>";
}
// Adicione um botão para voltar à página anterior ou à página inicial
echo "<div class='w3-padding'>";
echo "<a href='index.php' class='w3-button w3-teal w3-margin-top'>Voltar</a>";
echo "</div>";

// Feche a consulta e a conexão
$stmt_solicitacoes_pendentes->close();
$conn->close();
?>

