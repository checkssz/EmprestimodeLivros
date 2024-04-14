<?php
// Inicia a sessão
session_start();

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $login = $_POST['txtLogin'];
    $senha = $_POST['txtSenha'];

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

    // Consulta SQL para verificar se o usuário e a senha correspondem a um registro na tabela de usuários
    $sql = "SELECT * FROM usuario WHERE login = '$login' AND senha = '$senha'";
    $result = $conn->query($sql);

    // Verifica se a consulta retornou algum resultado
    if ($result->num_rows > 0) {
        // Usuário autenticado com sucesso
        $_SESSION['usuario_logado'] = true; // Define a sessão de usuário como logado
        $mensagem = "Login realizado com sucesso!";
    } else {
        // Caso contrário, exibe uma mensagem de erro
        $mensagem = "Usuário ou senha inválidos!";
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome/4.7.0/css/font-awesome.min.css">
 <title>Login</title>
</head>
<body class="w3-white">
<a href="index.php" class="w3-display-topleft ">
 <i class="fa fa-arrow-circle-left w3-large w3-teal w3-button w3-xxlarge"></i>
</a>
<div class="w3-padding w3-content w3-text-grey w3-third w3-margin w3-display-middle">
    <?php
    if (isset($mensagem)) {
        echo '<div class="w3-panel w3-teal w3-round-large w3-margin"><p class="w3-text-white">' . $mensagem . '</p></div>';
    }
    ?>
    <h1 class="w3-center w3-teal w3-round-large w3-margin">Login de Usuário</h1>
    <form action="loginAction.php" class="w3-container" method='post'>
        <label class="w3-text-teal" style="font-weight: bold;">Login</label>
        <input name="txtLogin" class="w3-input w3-light-grey w3-border"><br>
        <label class="w3-text-teal" style="font-weight: bold;">Senha</label>
        <input name="txtSenha" type="password" class="w3-input w3-light-grey w3-border"><br>
        <button name="btnAdd" class="w3-button w3-teal w3-round-large w3-right w3-margin-right">
            <i class="w3-xxlarge fa fa-sign-in"></i> Entrar
        </button>
    </form>
</div>
</body>
</html>
