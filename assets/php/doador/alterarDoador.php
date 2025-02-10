<?php
ob_start();
session_start();
require __DIR__ . '/../conexao.php';
$dbh = Conexao::getConexao();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Restrita</title>

    <link rel="stylesheet" href="../../css/dashboards.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        
        input,
        label {
            height: 25px !important;
            margin: 5px 0px 5px 2px;
        }

        input{
            width: 200px !important;
            height: 25px !important;
            margin-right: 10px !important;
            border: 1px dashed var(--color3) !important;
            border-radius: 10px !important;
        }

        input#alterEndereco {
            width: 480px !important;
        }

        input#alterDataNasc {
            width: 105px !important;
        }

        input#alterCpf {
            width: 210px !important;
        }

        input#alterSenha {
            width: 161px !important;
        }

    </style>
</head>

<body>
    <header>
        <button class="nav-toggle"><span class="material-symbols-outlined">
                menu
            </span></button>
        <figure class="logo"><img src="../../img/logo1.png" alt=""></figure>
        <div class="user-info">Bem-vindo, <?= $_SESSION['nome']; ?> <span id="username"></span></div>
    </header>
    <nav>
        <ul>
            <li><a href="pets.php">Meus Pet</a></li>
            <li><a href="doador_dashboard.php">Meu Perfil</a></li>
            <li><a href="chatsDoador.php">Conversas</a></li>
            <li><a href="../logout.php">Sair</a></li>
        </ul>
    </nav>

    <main>
        <div class="content" id="conteudo">

            <h1>Alterar Informações</h1>
            <?php 
            if(isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="alert">
                <span class="closebtn" onclick="this.parentElement.style.display = \'none\';">&times;</span>
                Dados Alterados com sucesso!
                </div>';
            }
        ?>      
            <form action="alterarDoador.php" method="post">

                <label for="alterNome">Nome: </label>
                <input type="text" name="alterNome" id="alterNome" required value="<?=$_SESSION['nome']?>">

                <label for="alterSobrenome">Sobrenome: </label>
                <input type="text" name="alterSobrenome" required value="<?=$_SESSION['sobrenome']?>">
                <br>
                <label for="dataNasc">Data de Nascimento: </label>
                <input type="date" name="alterDataNasc" id="alterDataNasc" value="<?=$_SESSION['data'] ?>">
                <br>
                <label for="cpf">CPF: </label>
                <input type="text" name="alterCpf" id="alterCpf" value="<?=$_SESSION['cpf'] ?>">
                <br>
                <label for="alterEndereco">Endereço: </label>
                <input type="text" name="alterEndereco" id="alterEndereco" value="<?=$_SESSION['endereco']?>">
                <br>
                <label for="alterEmail">E-mail</label>
                <input type="email" name="alterEmail" id="alterEmail" required autofocus value="<?=$_SESSION['email']?>">
                <br>
                <label for="alterSenha">Nova senha: </label>
                <input type="password" name="alterSenha" id="alterSenha">
                <br>
                <button class="salvar" type="submit">Salvar</button>
            </form>

        </div>
    </main>
    <script src="../../js/script.js"></script>
    <script src="../../js/alert.js"></script>

</body>

</html>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber dados do formulário
    $nome = $_POST['alterNome'];
    $sobrenome = $_POST['alterSobrenome'];
    $dataNasc = $_POST['alterDataNasc'];
    $cpf = $_POST['alterCpf'];
    $endereco = $_POST['alterEndereco'];
    $email = $_POST['alterEmail'];
    $senha = $_POST['alterSenha'];

    // Verificar se todos os campos estão preenchidos
    if (!empty($nome) && !empty($sobrenome) && !empty($dataNasc) && !empty($endereco) && !empty($email)) {
        // Atualizar os dados do usuário
        $sql = "UPDATE caopanheiro.usuario SET nome = :nome, sobrenome = :sobrenome, data_nascimento = :dataNasc, cpf = :cpf, endereco = :endereco, email = :email";
        
        // Se uma nova senha foi fornecida, atualizar também a senha
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql .= ", senha = :senha";
        }

        $sql .= " WHERE usuarioId = :id";

        // Preparar a consulta
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['usuId']);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':sobrenome', $sobrenome);
        $stmt->bindParam(':dataNasc', $dataNasc);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':email', $email);
        
        // Se uma nova senha foi fornecida, vinculá-la à consulta
        if (!empty($senha)) {
            $stmt->bindParam(':senha', $senhaHash);
        }

        // Executar a consulta
        $result = $stmt->execute();

        if ($result) {
            // Atualizar informações da sessão
            $_SESSION['nome'] = $nome;
            $_SESSION['sobrenome'] = $sobrenome;
            $_SESSION['data'] = $dataNasc;
            $_SESSION['cpf'] = $cpf;
            $_SESSION['endereco'] = $endereco;
            $_SESSION['email'] = $email;
            header("Location: alterarDoador.php?status=success");
            exit();
        } else {
            echo "Erro ao alterar os dados";
        }
    } else {
        echo "Todas as informações devem estar preenchidas!";
    }
}
ob_end_flush();
?>