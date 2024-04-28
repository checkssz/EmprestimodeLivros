<?php
// Configuração para exibir erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o arquivo de carregamento de variáveis de ambiente
require_once 'envLoader.php';

// Inicia a sessão
session_start();

// Carrega as variáveis de ambiente do arquivo .env
loadEnv(__DIR__ . '/.env');

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    $servername = getenv("DB_HOST");
    $username = getenv("DB_USER");
    $password = getenv("DB_PASSWORD");
    $dbname = getenv("DB_NAME");

    // Cria a conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Captura o ID do livro e do usuário
    $livro_id = isset($_POST['codigo_do_livro']) ? intval($_POST['codigo_do_livro']) : 0;
    $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;

    // Verifica se livro_id e usuario_id foram recebidos
    if ($livro_id === 0) {
        echo "<script>alert('Código do livro não recebido.');</script>";
        exit();
    }
    if ($usuario_id === 0) {
        echo "<script>alert('ID do usuário não recebido.');</script>";
        exit();
    }

    // Verifica se o livro existe na tabela 'livro'
    $sql_verificacao = "SELECT COUNT(*) FROM livro WHERE codigo_do_livro = ?";
    $stmt_verificacao = $conn->prepare($sql_verificacao);
    $stmt_verificacao->bind_param("i", $livro_id);
    $stmt_verificacao->execute();
    $stmt_verificacao->bind_result($livro_existe);
    $stmt_verificacao->fetch();
    $stmt_verificacao->close();

    if ($livro_existe > 0) {
        // Insere a declaração de interesse na tabela
        $sql = "INSERT INTO interesse (livro_id, usuario_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $livro_id, $usuario_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Interesse declarado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao declarar interesse: " . $stmt->error . "');</script>";
        }
        
        $stmt->close();
    } else {
        echo "<script>alert('O livro especificado não existe.');</script>";
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>
