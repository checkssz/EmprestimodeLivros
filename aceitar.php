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

// Obtenha o ID da solicitação da URL
$id_solicitacao = $_GET['id_emprestimo'];

// Atualize o status da solicitação para 'aceito'
$sql_aceitar = "UPDATE emprestimo SET status = 'aceito' WHERE id_emprestimo = ?";
$stmt_aceitar = $conn->prepare($sql_aceitar);
$stmt_aceitar->bind_param('i', $id_emprestimo);
$stmt_aceitar->execute();
// Consulta para remover a notificação correspondente à solicitação aceita
$sql_remover_notificacao = "DELETE FROM notificacoes WHERE mensagem LIKE ?";
$stmt_remover_notificacao = $conn->prepare($sql_remover_notificacao);
$mensagem_like = "%$id_emprestimo%";
$stmt_remover_notificacao->bind_param('s', $mensagem_like);
$stmt_remover_notificacao->execute();

// Feche a consulta
$stmt_remover_notificacao->close();

// Atualize o status da solicitação para 'aceito'
$sql_aceitar = "UPDATE emprestimo SET status = 'aceito' WHERE id_emprestimo = ?";
$stmt_aceitar = $conn->prepare($sql_aceitar);
$stmt_aceitar->bind_param('i', $id_emprestimo);
$stmt_aceitar->execute();

// Remova a notificação associada à solicitação aceita
$sql_remover_notificacao = "DELETE FROM notificacoes WHERE mensagem LIKE ?";
$stmt_remover_notificacao = $conn->prepare($sql_remover_notificacao);
$mensagem_like = "%$id_emprestimo%";
$stmt_remover_notificacao->bind_param('s', $mensagem_like);
$stmt_remover_notificacao->execute();
$stmt_remover_notificacao->close();

// Redirecionar para a página de notificações com mensagem de sucesso
header("Location: notificacoes.php?msg=Solicitação aceita com sucesso");
exit;

?>// Conexão com o banco de dados
