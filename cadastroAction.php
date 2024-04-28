<?php
// Configuração para exibir erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclua o carregamento de variáveis de ambiente
require_once 'envLoader.php';

// Inicie a sessão
session_start();

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

// Obtenha os dados do formulário e valide-os
$nome_completo = isset($_POST['nome_completo']) ? $_POST['nome_completo'] : '';
$data_de_nascimento = isset($_POST['data_de_nascimento']) ? $_POST['data_de_nascimento'] : '';
$endereco = isset($_POST['endereco']) ? $_POST['endereco'] : '';
$login = isset($_POST['login']) ? $_POST['login'] : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';

// Verifique se todos os campos obrigatórios estão preenchidos
if (empty($nome_completo) || empty($data_de_nascimento) || empty($endereco) || empty($login) || empty($senha)) {
    die("Erro: Todos os campos são obrigatórios.");
}

// Verifica se o login já está cadastrado
$sql_check_login = "SELECT id FROM usuario WHERE login = ?";
$stmt_check_login = $conn->prepare($sql_check_login);

// Use bind_param corretamente
$stmt_check_login->bind_param('s', $login);

// Execute a consulta preparada
$stmt_check_login->execute();
$result_check_login = $stmt_check_login->get_result();

if ($result_check_login->num_rows > 0) {
    // Se o login já estiver cadastrado
    echo "Erro: Este login já está cadastrado. Tente novamente.";
} else {
    // Criptografa a senha
    $senha_criptografada = password_hash($senha, PASSWORD_BCRYPT);

    // Insere o novo usuário na tabela de usuários
    $sql_insert_user = "INSERT INTO usuario (nome_completo, data_de_nascimento, endereco, login, senha) 
                        VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_user = $conn->prepare($sql_insert_user);

    // Use bind_param corretamente
    $stmt_insert_user->bind_param('sssss', $nome_completo, $data_de_nascimento, $endereco, $login, $senha_criptografada);

    if ($stmt_insert_user->execute()) {
        // Cadastro bem-sucedido
        echo "Cadastro realizado com sucesso!";
        // Redirecione para a página de login
        header("Location: login.php");
        exit;
    } else {
        // Erro ao realizar cadastro
        echo "Erro ao realizar o cadastro: " . $stmt_insert_user->error;
    }
}

// Feche as consultas e a conexão
$stmt_check_login->close();
$stmt_insert_user->close();
$conn->close();
?>
