<?php
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
    // Obtém os dados do formulário
    $login = $_POST['txtLogin'];
    $senha_inserida = $_POST['txtSenha'];

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

// Consulta SQL para buscar o ID e a senha criptografada do usuário com base no login
$sql = "SELECT id, senha FROM usuario WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $login);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o login foi encontrado
if ($result->num_rows > 0) {
    // Obtenha o ID e a senha criptografada
    $row = $result->fetch_assoc();
    $usuario_id = $row['id'];
    $senha_criptografada = $row['senha'];

    // Verifica a senha inserida pelo usuário em relação à senha criptografada
    if (password_verify($senha_inserida, $senha_criptografada)) {
        // Login bem-sucedido
        $_SESSION['usuario_logado'] = true;
        // Defina a chave 'id' na sessão com o ID do usuário
        $_SESSION['id'] = $usuario_id;
        $mensagem = "Login realizado com sucesso!";
    } else {
        // Senha incorreta
        $mensagem = "Senha incorreta!";
    }
} else {
    // Login não encontrado
    $mensagem = "Login não encontrado!";
}

// Fecha as consultas
$stmt->close();
// Fecha a conexão com o banco de dados
$conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Login</title>
</head>
<body class="w3-white">
    <a href="index.php" class="w3-display-topleft">
        <i class="fa fa-arrow-circle-left w3-large w3-teal w3-button w3-xxlarge"></i>
    </a>
    <div class="w3-padding w3-content w3-text-grey w3-third w3-margin w3-display-middle">
        <?php
        if (isset($mensagem)) {
            echo '<div class="w3-panel w3-teal w3-round-large w3-margin"><p class="w3-text-white">' . $mensagem . '</p></div>';
        }
        ?>
        <h1 class="w3-center w3-teal w3-round-large w3-margin">Login de Usuário</h1>
        <form action="loginAction.php" class="w3-container" method="post">
     
