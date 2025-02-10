<?php
ob_start();
session_start();
require __DIR__ . '/../../conexao.php';
$dbh = Conexao::getConexao();

// Verificar se o ID do usuário está presente na query string
if (isset($_GET['Id'])) {
    $usuId = intval($_GET['Id']);

    // Buscar os dados do usuário no banco de dados
    $sql = "SELECT * FROM caopanheiro.usuario WHERE usuarioId = :usuarioId";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':usuarioId', $usuId);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o usuário foi encontrado
    if ($usuario) {
        // Dados do usuário encontrados, agora você pode preencher o formulário com esses dados
        $nome = $usuario['nome'];
        $sobrenome = $usuario['sobrenome'];
        $data = $usuario['data_nascimento'];
        $endereco = $usuario['endereco'];
        $email = $usuario['email'];
    } else {
        // Se o usuário não for encontrado, você pode redirecionar para uma página de erro ou tomar outra ação adequada
        echo "Usuário não encontrado!";
        exit();
    }
} else {
    // Se o ID do usuário não estiver presente na query string, redirecionar para uma página de erro ou tomar outra ação adequada
    echo "ID do usuário não fornecido!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Restrita</title>

    <link rel="stylesheet" href="../../../css/dashboards.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        input,
        label {
            height: 25px !important;
            margin: 5px 0px 5px 2px;
        }

        input {
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
        <figure class="logo"><img src="../../../img/logo1.png" alt=""></figure>
        <div class="user-info">Bem-vindo,
            <?= $_SESSION['nome']; ?> <span id="username"></span>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="../crud-pet/pets.php">Pets cadastrados</a></li>
            <li><a href="listaUsuarios.php">Usuários cadastrados</a></li>
            <li><a href="../administrador_dashboard.php">Meu Perfil</a></li>
            <li><a href="../listaAdmin.php">Administradores</a></li>
            <li><a href="../../logout.php">Sair</a></li>
        </ul>
    </nav>

    <main>
        <div class="content" id="conteudo">

            <h1>Alterar Informações</h1>
            <?php
            if (isset($_GET['status']) && $_GET['status'] == 'success') {
                echo '<div class="alert">
                <span class="closebtn" onclick="this.parentElement.style.display = \'none\';">&times;</span>
                Dados Alterados com sucesso!
                </div>';
            }
            ?>
            <form action="alterar.php" method="post">
                <input type="hidden" name="usuId" value="<?= $usuId ?>">
                <label for="alterNome">Nome: </label>
                <input type="text" name="alterNome" id="alterNome" required value="<?= $nome ?>">

                <label for="alterSobrenome">Sobrenome: </label>
                <input type="text" name="alterSobrenome" required value="<?= $sobrenome ?>">
                <br>
                <label for="dataNasc">Data de Nascimento: </label>
                <input type="date" name="alterDataNasc" id="alterDataNasc" value="<?= $data ?>">
                <br>
                <label for="alterEndereco">Endereço: </label>
                <input type="text" name="alterEndereco" id="alterEndereco" value="<?= $endereco ?>">
                <br>
                <label for="alterEmail">E-mail</label>
                <input type="email" name="alterEmail" id="alterEmail" required autofocus value="<?= $email ?>"><br>

                <label for="alterSenha">Nova senha: </label>
                <input type="password" name="alterSenha" id="alterSenha">
                <br>
                <button class="salvar" type="submit">Salvar</button>
            </form>

        </div>
    </main>
    <script src="../../../js/script.js"></script>
    <script src="../../../js/alert.js"></script>

</body>

</html>