<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="w3-container w3-teal">
        <h1>Cadastro de Usuário</h1>
    </header>
    <main class="w3-container">
        <h2>Cadastre-se</h2>
        <form action="cadastroAction.php" method="POST" class="w3-container">
            <div class="w3-section">
                <label for="nome_completo">Nome Completo:</label>
                <input type="text" name="nome_completo" id="nome_completo" class="w3-input" required>
            </div>
            <div class="w3-section">
                <label for="data_de_nascimento">Data de Nascimento:</label>
                <input type="date" name="data_de_nascimento" id="data_de_nascimento" class="w3-input" required>
            </div>
            <div class="w3-section">
                <label for="endereco">Endereço:</label>
                <input type="text" name="endereco" id="endereco" class="w3-input" required>
            </div>
            <div class="w3-section">
                <label for="login">Login:</label>
                <input type="text" name="login" id="login" class="w3-input" required>
            </div>
            <div class="w3-section">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" class="w3-input" required>
            </div>
            <div class="w3-section">
                <button type="submit" class="w3-button w3-teal">Cadastrar</button>
            </div>
        </form>
    </main>
    <footer class="w3-container w3-teal">
        <p>&copy; 2024 Sistema de Compartilhamento de Livros</p>
    </footer>
</body>
</html>
