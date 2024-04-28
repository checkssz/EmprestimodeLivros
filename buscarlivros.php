<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Buscar Livros</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script>
        // Função para buscar livros com base na consulta do usuário
        function buscarLivros() {
            const query = document.getElementById('busca').value;
            const xhr = new XMLHttpRequest();

            // Envia uma requisição GET com a consulta ao servidor
            xhr.open('GET', `buscarlivrosAction.php?query=${encodeURIComponent(query)}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Atualiza a lista de livros com o resultado da consulta
                    document.getElementById('resultado').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body class="w3-container w3-light-grey">
    <h1 class="w3-center w3-teal w3-round-large">Buscar Livros</h1>

    <!-- Barra de Pesquisa -->
    <div class="w3-container w3-center w3-margin">
        <input type="text" id="busca" class="w3-input w3-border w3-round-large" placeholder="Digite o título do livro" onkeyup="buscarLivros()">
    </div>

    <!-- Resultados da Pesquisa -->
    <div id="resultado" class="w3-container w3-card-4 w3-margin w3-white">
        <!-- Os resultados serão atualizados aqui -->
        <?php
        // Inclui o arquivo de carregamento de variáveis de ambiente
        require_once 'envLoader.php';

        // Carrega as variáveis de ambiente do arquivo .env
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

        // Consulta para obter todos os livros disponíveis inicialmente
        $sql = "SELECT codigo_do_livro, nome_do_livro, autor, descricao_do_livro FROM livro";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<ul class="w3-ul w3-hoverable">';
            while ($livro = $result->fetch_assoc()) {
                // Adiciona um link ao título do livro que redireciona para detalheslivro.php com o código do livro
                echo '<li>';
                echo '<a href="detalheslivro.php?codigo_do_livro=' . $livro['codigo_do_livro'] . '">';
                echo '<strong>' . $livro['nome_do_livro'] . '</strong> - ' . $livro['autor'];
                echo '</a><br>';
                echo '<small>' . $livro['descricao_do_livro'] . '</small>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="w3-text-gray">Nenhum livro disponível.</p>';
        }

        $conn->close();
        ?>
    </div>

    <!-- Botão para Voltar à Página Inicial -->
    <div class="w3-container w3-center w3-margin">
        <a href="index.php" class="w3-button w3-blue w3-round-large">Voltar à Página Inicial</a>
    </div>
</body>
</html>
