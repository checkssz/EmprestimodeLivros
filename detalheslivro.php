<?php
// Configuração para exibir erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o arquivo de carregamento de variáveis de ambiente
require_once 'envLoader.php';

// Carrega as variáveis de ambiente do arquivo .env
loadEnv(__DIR__ . '/.env');

// Obtenha o código do livro da URL
$codigo_do_livro = isset($_GET['codigo_do_livro']) ? intval($_GET['codigo_do_livro']) : '';

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

// Obtenha as informações do livro com base no código do livro
$sql_livro = "SELECT nome_do_livro, descricao_do_livro, genero, autor, ano_de_publicacao, idioma, codigo_do_livro
              FROM livro
              WHERE codigo_do_livro = ?";
$stmt_livro = $conn->prepare($sql_livro);
$stmt_livro->bind_param('i', $codigo_do_livro);
$stmt_livro->execute();
$result_livro = $stmt_livro->get_result();

// Verifique se o livro foi encontrado
if ($result_livro->num_rows == 0) {
    echo '<p class="w3-text-red">Livro não encontrado.</p>';
    exit;
}

// Obtenha as informações do livro
$livro = $result_livro->fetch_assoc();

// Consulta para obter os donos dos livros disponíveis para empréstimo
$sql_usuarios = "SELECT u.nome_completo AS nome_usuario, u.login AS email_usuario, l.usuario_id_dono
                FROM usuario u
                JOIN livro l ON u.id = l.usuario_id_dono
                WHERE l.codigo_do_livro = ? AND l.disponivel = 1";

// Prepare a consulta e execute
$stmt_usuarios = $conn->prepare($sql_usuarios);
$stmt_usuarios->bind_param('i', $codigo_do_livro);
$stmt_usuarios->execute();
$result_usuarios = $stmt_usuarios->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Livro</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script>
        function abrirModal() {
            document.getElementById("modalSolicitarEmprestimo").style.display = "block";
        }
        
        function fecharModal() {
            document.getElementById("modalSolicitarEmprestimo").style.display = "none";
        }
    </script>
</head>
<body class="w3-container w3-light-grey">
    <h1 class="w3-center w3-teal w3-round-large">Detalhes do Livro</h1>

    <!-- Informações do Livro -->
    <div class="w3-container w3-card-4 w3-margin w3-white">
        <h2 class="w3-teal w3-padding"><?php echo htmlspecialchars($livro['nome_do_livro']); ?></h2>
        <p><strong>Descrição:</strong> <?php echo htmlspecialchars($livro['descricao_do_livro']); ?></p>
        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($livro['genero']); ?></p>
        <p><strong>Autor:</strong> <?php echo htmlspecialchars($livro['autor']); ?></p>
        <p><strong>Ano de Publicação:</strong> <?php echo htmlspecialchars($livro['ano_de_publicacao']); ?></p>
        <p><strong>Idioma:</strong> <?php echo htmlspecialchars($livro['idioma']); ?></p>
    </div>

    <!-- Botão para abrir a janela modal -->
    <div class="w3-container w3-center w3-margin">
        <button onclick="abrirModal()" class="w3-button w3-blue w3-round-large">Solicitar Empréstimo</button>
    </div>
    
    <!-- Janela Modal para Solicitar Empréstimo -->
    <div id="modalSolicitarEmprestimo" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-opacity w3-round-large">
            <span class="w3-button w3-display-topright" onclick="fecharModal()">×</span>
            <div class="w3-container">
                <h2>Solicitar Empréstimo</h2>
                <form action="processar_emprestimo.php" method="post">
    <!-- Insere um campo oculto com o código do livro -->
    <input type="hidden" name="codigo_do_livro" value="<?php echo htmlspecialchars($livro['codigo_do_livro']); ?>">

    <label for="data_inicio">Data de Início:</label>
    <input type="date" id="data_inicio" name="data_de_inicio_de_emprestimo" required class="w3-input w3-border">

    <!-- Campos para preencher as informações do empréstimo -->
    <label for="data_fim">Data de Devolução:</label>
    <input type="date" id="data_fim" name="data_de_fim_de_emprestimo" required class="w3-input w3-border">

    <label for="mensagem">Mensagem para o dono do livro:</label>
    <textarea id="mensagem" name="mensagem" class="w3-input w3-border" placeholder="Opcional"></textarea>

    <!-- Adicionando um dropdown para selecionar o dono do livro -->
    <label for="dono_livro">Escolha a pessoa para o empréstimo:</label>
    <select id="dono_livro" name="usuario_id_dono" class="w3-select w3-border" required>
        <?php
        // Preenchendo o menu suspenso com os usuários que possuem o livro disponível para empréstimo
        while ($usuario = $result_usuarios->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($usuario['usuario_id_dono']) . '">' . htmlspecialchars($usuario['nome_usuario']) . ' - ' . htmlspecialchars($usuario['email_usuario']) . '</option>';
        }
        ?>
    </select>

    <button type="submit" class="w3-button w3-teal w3-round-large">Solicitar Empréstimo</button>
</form>
            </div>
        </div>
    </div>

    <!-- Usuários com o Livro Disponível para Empréstimo -->
<div class="w3-container w3-card-4 w3-margin w3-white">
    <h2 class="w3-teal w3-padding">Dono do Livro</h2>
    <?php
    // Consulta para obter as informações do dono do livro
    $sql_dono = "SELECT u.nome_completo AS nome_usuario, u.login AS email_usuario
                FROM usuario u
                JOIN livro l ON u.id = l.usuario_id_dono
                WHERE l.codigo_do_livro = ?";
    
    // Prepare a consulta e execute
    $stmt_dono = $conn->prepare($sql_dono);
    $stmt_dono->bind_param('i', $codigo_do_livro);
    $stmt_dono->execute();
    $result_dono = $stmt_dono->get_result();

    // Verifique se o dono do livro foi encontrado
    if ($result_dono->num_rows > 0) {
        $dono = $result_dono->fetch_assoc();
        ?>
        <ul class="w3-ul w3-hoverable">
            <li>
                <?php echo htmlspecialchars($dono['nome_usuario']); ?> - <?php echo htmlspecialchars($dono['email_usuario']); ?>
            </li>
        </ul>
    <?php } else { ?>
        <p class="w3-text-gray">Dono do livro não encontrado.</p>
    <?php } ?>
</div>


<!-- Botão para Voltar à Página Anterior -->
    <div class="w3-container w3-center w3-margin">
        <a href="javascript:history.back()" class="w3-button w3-blue w3-round-large">Voltar</a>
    </div>
</body>
</html>
