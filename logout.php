<?php
// Inicia a sessão
session_start();

// Destroi a sessão
session_destroy();

// Redireciona o usuário de volta para a página de login
header("Location: login.php");
exit;
?>
