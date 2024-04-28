<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Livro</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body class="w3-container w3-padding">

    <h2 class="w3-teal w3-padding">Cadastro de Livro</h2>
    
    <form action="cadastrolivroAction.php" method="POST" class="w3-card-4 w3-padding w3-margin w3-light-grey w3-round-large">
        <input type="hidden" name="usuario_id_dono" value="<?php echo $_SESSION['usuario_id']; ?>">

        <div class="w3-section">
            <label for="nome_livro" class="w3-text-teal">Nome do Livro:</label>
            <input type="text" id="nome_livro" name="nome_livro" class="w3-input w3-border w3-light-grey" required>
        </div>
        
        <div class="w3-section">
            <label for="descricao_livro" class="w3-text-teal">Descrição:</label>
            <textarea id="descricao_livro" name="descricao_livro" class="w3-input w3-border w3-light-grey" required></textarea>
        </div>
        
        <div class="w3-section">
            <label for="genero" class="w3-text-teal">Gênero:</label>
            <input type="text" id="genero" name="genero" class="w3-input w3-border w3-light-grey" required>
        </div>
        
        <div class="w3-section">
            <label for="autor" class="w3-text-teal">Autor:</label>
            <input type="text" id="autor" name="autor" class="w3-input w3-border w3-light-grey" required>
        </div>
        
        <div class="w3-section">
            <label for="ano_publicacao" class="w3-text-teal">Ano de Publicação:</label>
            <input type="number" id="ano_publicacao" name="ano_publicacao" class="w3-input w3-border w3-light-grey" required>
        </div>
        
        <div class="w3-section">
            <label for="idioma" class="w3-text-teal">Idioma:</label>
            <input type="text" id="idioma" name="idioma" class="w3-input w3-border w3-light-grey" required>
        </div>
        
        <button type="submit" class="w3-button w3-teal w3-round-large w3-right">Cadastrar Livro</button>
    </form>

</body>
</html>
