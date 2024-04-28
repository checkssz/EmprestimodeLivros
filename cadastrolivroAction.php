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

// Conexão com o banco de dados
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$dbname = getenv("DB_NAME");

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Recupera os dados do formulário
$nome_livro = $_POST['nome_livro'] ?? '';
$descricao_livro = $_POST['descricao_livro'] ?? '';
$genero = $_POST['genero'] ?? '';
$autor = $_POST['autor'] ?? '';
$ano_publicacao = $_POST['ano_publicacao'] ?? '';
$idioma = $_POST['idioma'] ?? '';
$usuario_id_dono = $_SESSION['id']; // Atribua o ID do usuário que está logado como dono do livro

// Define o livro como disponível para empréstimo por padrão
$disponivel = 1;

// Prepara a query SQL para inserir o livro no banco de dados
$sql = $conn->prepare("INSERT INTO Livro (nome_do_livro, descricao_do_livro, genero, autor, ano_de_publicacao, idioma, disponivel, usuario_id_dono) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Verifique se a preparação da consulta foi bem-sucedida
if ($sql) {
    // Vincula os parâmetros à consulta preparada
    $sql->bind_param("ssssisis", $nome_livro, $descricao_livro, $genero, $autor, $ano_publicacao, $idioma, $disponivel, $usuario_id_dono);
    
    // Execute a consulta
    if ($sql->execute()) {
        echo "Livro cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar o livro: " . $sql->error;
    }

    // Fecha a consulta preparada
    $sql->close();
} else {
    echo "Erro ao preparar a consulta: " . $conn->error;
}

// Fecha a conexão com o banco de dados
$conn->close();

?>
