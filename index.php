<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Compartilhamento de Livros</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="w3-container w3-teal">
        <h1>Sistema de Compartilhamento de Livros</h1>
        <nav class="w3-bar w3-teal">
            <a href="#" class="w3-bar-item w3-button">Início</a>
            <?php
            session_start(); // Inicia a sessão

            if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado']) {
                echo '
                    <a href="#" class="w3-bar-item w3-button">Meus Livros</a>
                    <a href="#" class="w3-bar-item w3-button">Buscar Livros</a>
                    <a href="logout.php" class="w3-bar-item w3-button">Sair</a>
                ';
            } else {
                echo '<a href="login.php" class="w3-bar-item w3-button">Entrar</a>';
            }
            ?>
        </nav>
    </header>
    <main class="w3-container">
        <section class="w3-container w3-center">
            <h2>Bem-vindo ao Sistema de Compartilhamento de Livros</h2>
            <p>Aqui você pode encontrar e compartilhar livros com outros usuários.</p>
            <a href="#" class="w3-button w3-teal w3-round-large">Começar</a>
        </section>
    </main>
    <footer class="w3-container w3-teal">
        <p>&copy; 2024 Sistema de Compartilhamento de Livros</p>
    </footer>
</body>
</html>
