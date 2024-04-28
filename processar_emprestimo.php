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

// Obtenha os dados enviados pelo formulário
$livro_id = isset($_POST['codigo_do_livro']) ? intval($_POST['codigo_do_livro']) : 0;
$usuario_id_dono = isset($_POST['usuario_id_dono']) ? intval($_POST['usuario_id_dono']) : 0;
$data_inicio = isset($_POST['data_de_inicio_de_emprestimo']) ? date('Y-m-d', strtotime($_POST['data_de_inicio_de_emprestimo'])) : '';
$data_fim = isset($_POST['data_de_fim_de_emprestimo']) ? date('Y-m-d', strtotime($_POST['data_de_fim_de_emprestimo'])) : '';
$mensagem = isset($_POST['mensagem']) ? $_POST['mensagem'] : '';

var_dump($_POST);

// Depuração: Verificar os valores recebidos
var_dump($livro_id);
var_dump($usuario_id_dono);
var_dump($data_inicio);
var_dump($data_fim);
var_dump($mensagem);

// Verifique se todos os dados necessários foram fornecidos
if ($livro_id === 0 || $usuario_id_dono === 0 || empty($data_inicio) || empty($data_fim)) {
    die('Erro: Dados incompletos para solicitar empréstimo.');
}

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

// Insere um novo registro na tabela de empréstimos
$sql = "INSERT INTO emprestimo (codigo_do_livro, data_de_inicio_de_emprestimo, data_de_fim_de_emprestimo, usuario_id_solicitante, usuario_id_dono, status, mensagem)
        VALUES (?, ?, ?, ?, ?, 'averiguando', ?)";

// Prepara a consulta
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erro na preparação da consulta: " . $conn->error);
}

// Obtenha o ID do usuário solicitante a partir da sessão
$usuario_id_solicitante = isset($_SESSION['id']) ? intval($_SESSION['id']) : null;
if ($usuario_id_solicitante === null) {
    die('Erro: Usuário solicitante não encontrado na sessão.');
}

// Liga os parâmetros para a consulta preparada
$stmt->bind_param('sssisi', $livro_id, $data_inicio, $data_fim, $usuario_id_solicitante, $usuario_id_dono, $mensagem);

// Executa a consulta
if ($stmt->execute()) {
    // Notifica o dono do livro
    notificar_dono_do_livro($usuario_id_dono, $usuario_id_solicitante, $mensagem);
    
    // Envia uma resposta ao usuário solicitante
    echo "Solicitação de empréstimo enviada com sucesso!";
} else {
    echo "Erro ao enviar a solicitação de empréstimo: " . $stmt->error;
}

// Fecha a consulta preparada e a conexão com o banco de dados
$stmt->close();
$conn->close();

// Função para notificar o dono do livro sobre a solicitação de empréstimo
function notificar_dono_do_livro($usuario_id_dono, $usuario_id_solicitante, $mensagem) {
    global $conn;

    // Crie uma mensagem de notificação para o dono do livro
    $mensagem_notificacao = "Você recebeu uma solicitação de empréstimo do usuário ID {$usuario_id_solicitante} para o livro ID {$usuario_id_dono}. Mensagem: {$mensagem}";

    // Insere a notificação na tabela notificacoes
    $sql = "INSERT INTO notificacoes (usuario_id, mensagem) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta para inserir notificação: " . $conn->error);
    }

    $stmt->bind_param("is", $usuario_id_dono, $mensagem_notificacao);
    $stmt->execute();
    $stmt->close();
}
?>
