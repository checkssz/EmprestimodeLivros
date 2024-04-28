<?php
// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Obtenha o ID da solicitação da URL
$id_solicitacao = $_GET['id'];

// Atualize o status da solicitação para 'recusado'
$sql_recusar = "UPDATE emprestimo SET status = 'recusado' WHERE id = ?";
$stmt_recusar = $conn->prepare($sql_recusar);
$stmt_recusar->bind_param('i', $id_solicitacao);
$stmt_recusar->execute();

// Notifique o solicitante ou redirecione
header("Location: notificacoes.php?msg=Solicitação recusada");

?>