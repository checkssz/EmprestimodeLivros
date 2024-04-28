<?php
// Ativação de exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o arquivo de carregamento de variáveis de ambiente
require_once 'envLoader.php';

// Inicia a sessão
session_start();

// Carrega as variáveis de ambiente do arquivo .env
loadEnv(__DIR__ . '/.env');

// Verifique se o usuário está autenticado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Obtenha o ID do usuário da sessão
$usuario_id = $_SESSION['id'];

// Conecte-se ao banco de dados
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$dbname = getenv("DB_NAME");

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão com o banco de dados
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Consulta para obter livros disponíveis para empréstimo
$sql_emprestar = "SELECT l.codigo_do_livro, l.nome_do_livro, l.autor, l.descricao_do_livro
                  FROM livro l
                  LEFT JOIN emprestimo e ON l.codigo_do_livro = e.codigo_do_livro
                  WHERE e.codigo_do_livro IS NULL AND l.usuario_id_dono = ?";


$stmt_emprestar = $conn->prepare($sql_emprestar);
$stmt_emprestar->bind_param('i', $usuario_id);
$stmt_emprestar->execute();
$result_emprestar = $stmt_emprestar->get_result();

// Consulta para obter os livros de interesse do usuário
$sql_interesse = "SELECT l.codigo_do_livro, l.nome_do_livro, l.autor, l.descricao_do_livro
                  FROM livro l
                  JOIN interesse i ON l.codigo_do_livro = i.livro_id
                  WHERE i.usuario_id = ?";

$stmt_interesse = $conn->prepare($sql_interesse);
$stmt_interesse->bind_param("i", $usuario_id);
$stmt_interesse->execute();
$result_interesse = $stmt_interesse->get_result();

// Fecha a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Meus Livros</title>
</head>
<body class="w3-container w3-light-grey">
    <h1 class="w3-center w3-teal w3-round-large">Meus Livros</h1>

    <!-- Livros para Empréstimo -->
    <div class="w3-container w3-card-4 w3-margin w3-white">
        <h2 class="w3-teal w3-padding">Livros para Empréstimo</h2>
        <?php if ($result_emprestar->num_rows > 0): ?>
            <ul class="w3-ul w3-hoverable">
                <?php while ($livro = $result_emprestar->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo $livro['nome_do_livro']; ?></strong> - <?php echo $livro['autor']; ?><br>
                        <small><?php echo $livro['descricao_do_livro']; ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="w3-text-gray">Nenhum livro disponível para empréstimo no momento.</p>
        <?php endif; ?>
    </div>

    <!-- Livros de Interesse -->
    <div class="w3-container w3-card-4 w3-margin w3-white">
        <h2 class="w3-teal w3-padding">Livros de Interesse</h2>
        <?php if ($result_interesse->num_rows > 0): ?>
            <ul class="w3-ul w3-hoverable">
                <?php while ($interesse = $result_interesse->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo $interesse['nome_do_livro']; ?></strong> - <?php echo $interesse['autor']; ?><br>
                        <small><?php echo $interesse['descricao_do_livro']; ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="w3-text-gray">Nenhum livro de interesse no momento.</p>
        <?php endif; ?>
    </div>

    <!-- Botão para Cadastro de Livros -->
    <div class="w3-container w3-center w3-margin">
        <a href="cadastrolivro.php" class="w3-button w3-teal w3-round-large">Cadastrar Livro</a>
    </div>

       <!-- Botão para Voltar à Página Inicial -->
       <div class="w3-container w3-center w3-margin">
        <a href="index.php" class="w3-button w3-blue w3-round-large">Voltar à Página Inicial</a>
    </div>
</body>
</html>
