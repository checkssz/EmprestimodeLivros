<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
                    <a href="meuslivros.php" class="w3-bar-item w3-button">Meus Livros</a>
                    <a href="buscarlivros.php" class="w3-bar-item w3-button">Buscar Livros</a>
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
            <a href="buscarlivros.php" class="w3-button w3-teal w3-round-large">Começar</a>
        </section>

        <?php if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado']): ?>
        <!-- Seção de Notificações -->
        <section class="w3-container w3-margin-top">
            <h3>Notificações</h3>
            <?php
            // Inclua o carregamento de variáveis de ambiente
            require_once 'envLoader.php';
            
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
            
            // Obtenha o ID do usuário logado
            $usuario_id = $_SESSION['id'];
            
            // Consulta para obter notificações com o nome do usuário solicitante
            $sql_notificacoes = "SELECT n.mensagem, u.nome_completo
                                 FROM notificacoes n
                                 JOIN usuario u ON n.usuario_id = u.id
                                 WHERE n.usuario_id = ?";
            
            // Prepare a consulta
            $stmt_notificacoes = $conn->prepare($sql_notificacoes);
            $stmt_notificacoes->bind_param('i', $usuario_id);
            $stmt_notificacoes->execute();
            $result_notificacoes = $stmt_notificacoes->get_result();
            
            // Exibir notificações
            if ($result_notificacoes->num_rows > 0) {
                echo "<ul>";
                while ($row = $result_notificacoes->fetch_assoc()) {
                    echo "<li>Mensagem: " . htmlspecialchars($row['mensagem']) . " - Solicitante: " . htmlspecialchars($row['nome_completo']) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Não há notificações no momento.</p>";
            }
            
            // Feche a consulta de notificações
            $stmt_notificacoes->close();
            $conn->close();
            ?>
            <a href="solicitacoes.php" class="w3-button w3-teal">Ver Solicitações de Empréstimo</a>
        </section>

        <!-- Seção de Livros Solicitados -->
        <section class="w3-container w3-margin-top">
            <h3>Livros Solicitados</h3>
            <?php
            // Reabra a conexão com o banco de dados
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Erro de conexão: " . $conn->connect_error);
            }

            // Consulta para obter livros solicitados pelo usuário
            $sql_livros_solicitados = "SELECT e.codigo_do_livro, l.nome_do_livro, e.data_de_inicio_de_emprestimo, e.data_de_fim_de_emprestimo, e.status
                                      FROM emprestimo e
                                      INNER JOIN Livro l ON e.codigo_do_livro = l.codigo_do_livro
                                      WHERE e.usuario_id_solicitante = ?";
            
            // Prepare a consulta
            $stmt_livros_solicitados = $conn->prepare($sql_livros_solicitados);
            $stmt_livros_solicitados->bind_param('i', $usuario_id);
            $stmt_livros_solicitados->execute();
            $result_livros_solicitados = $stmt_livros_solicitados->get_result();
            
            // Exibir livros solicitados
            if ($result_livros_solicitados->num_rows > 0) {
                echo "<ul>";
                while ($row = $result_livros_solicitados->fetch_assoc()) {
                    echo "<li>{$row['nome_do_livro']} - Início: {$row['data_de_inicio_de_emprestimo']} | Fim: {$row['data_de_fim_de_emprestimo']} | Status: {$row['status']}</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Não há livros solicitados no momento.</p>";
            }

            // Feche a consulta e a conexão
            $stmt_livros_solicitados->close();
            $conn->close();
            ?>
        </section>

        <!-- Seção de Livros Emprestados -->
        <section class="w3-container w3-margin-top">
            <h3>Livros Emprestados</h3>
            <?php
            // Reabra a conexão com o banco de dados
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Erro de conexão: " . $conn->connect_error);
            }

            // Consulta para obter livros emprestados pelo usuário
            $sql_livros_emprestados = "SELECT e.codigo_do_livro, l.nome_do_livro, e.data_de_inicio_de_emprestimo, e.data_de_inicio_de_emprestimo, e.status
                                      FROM emprestimo e
                                      INNER JOIN Livro l ON e.codigo_do_livro = l.codigo_do_livro
                                      WHERE l.usuario_id_dono = ?";
            
            // Prepare a consulta
            $stmt_livros_emprestados = $conn->prepare($sql_livros_emprestados);
            $stmt_livros_emprestados->bind_param('i', $_SESSION['id']);
            $stmt_livros_emprestados->execute();
            $result_livros_emprestados = $stmt_livros_emprestados->get_result();
            
            // Exibir livros emprestados
            if ($result_livros_emprestados->num_rows > 0) {
                echo "<ul>";
                while ($row = $result_livros_emprestados->fetch_assoc()) {
                    echo "<li>{$row['nome_do_livro']} - Início: {$row['data_de_inicio_de_emprestimo']} | Fim: {$row['data_de_fim_de_emprestimo']} | Status: {$row['status']}</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Não há livros emprestados no momento.</p>";
            }

            // Feche a consulta e a conexão
            $stmt_livros_emprestados->close();
            $conn->close();
            ?>
        </section>
        <?php endif; ?>
    </main>

    <footer class="w3-container w3-teal">
        <p>&copy; 2024 Sistema de Compartilhamento de Livros</p>
    </footer>
</body>

</html>
