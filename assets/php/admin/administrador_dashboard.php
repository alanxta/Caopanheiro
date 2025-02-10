<?php
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
</head>

<body>
    <header>
        <button class="nav-toggle"><span class="material-symbols-outlined">
                menu
            </span></button>
        <figure class="logo"><img src="../../img/logo1.png" alt=""></figure>
        <div class="user-info">Bem-vindo,
            <?= $_SESSION['nome']; ?> <span id="username"></span>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="crud-pet/pets.php">Pets cadastrados</a></li>
            <li><a href="crud-usuario/listaUsuarios.php">Usuários cadastrados</a></li>
            <li><a href="administrador_dashboard.php">Meu Perfil</a></li>
            <li><a href="listaAdmin.php">Administradores</a></li>
            <li><a href="../logout.php">Sair</a></li>
        </ul>
    </nav>
    <main>
        <div class="content" id="conteudo">
            <h1>Minhas Informações</h1>
            <h2>Nome: </h2>
            <p>
                <?= $_SESSION['nome']; ?>
            </p>
            <h2>Sobrenome: </h2>
            <p>
                <?= $_SESSION['sobrenome']; ?>
            </p>
            <br>
            <h2>E-mail: </h2>
            <p>
                <?= $_SESSION['email']; ?>
            </p>
            <br>
            <h2>Tipo de Perfil: </h2>
            <p>
                <?= $_SESSION['perfil']; ?>
            </p>
            <br>
            <button id="usu"><a class="btnalterar" href="crud-admin/alterarAdmin.php?id=<?= $_SESSION['usuId']; ?>">Alterar</a></button><br><br>
            <div id="delete"><button id="usu"><a class="btnexcluir" href="crud-admin/excluirAdmin.php?id=<?= $_SESSION['usuId']; ?>" onclick="return confirm('Essa operação não tem retorno. Deseja confirmar?');">Excluir
                        Conta</a></button>
            </div>

            <?php $dbh = null; ?>
            </section>

        </div>
    </main>
    <script src="../../js/script.js">

    </script>
</body>

</html>