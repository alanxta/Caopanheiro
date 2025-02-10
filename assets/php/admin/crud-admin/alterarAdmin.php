<?php
ob_start();
session_start();
require __DIR__ . '/../../conexao.php';
$dbh = Conexao::getConexao();
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

        input#alterDataNasc {
            width: 105px !important;
        }

        input#alterSenha {
            width: 161px !important;
        }
    </style>
</head>

<body>
    <header>
        <button class="nav-toggle"><span class="material-symbols-outlined">menu</span></button>
        <figure class="logo"><img src="../../../img/logo1.png" alt="Logo"></figure>
        <div class="user-info">Bem-vindo,
            <?= htmlspecialchars($_SESSION['nome']); ?> <span id="username"></span>
        </div>
    </header>
    <nav>
    <ul>
      <li><a href="../crud-pet/pets.php">Pets cadastrados</a></li>
      <li><a href="../crud-usuario/listaUsuarios.php">Usuários cadastrados</a></li>
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
            <form action="alterarAdmin.php" method="post">
                <label for="alterNome">Nome: </label>
                <input type="text" name="alterNome" id="alterNome" required value="<?= htmlspecialchars($_SESSION['nome']); ?>">

                <label for="alterSobrenome">Sobrenome: </label>
                <input type="text" name="alterSobrenome" required value="<?= htmlspecialchars($_SESSION['sobrenome']); ?>"><br>

                <label for="alterEmail">E-mail</label>
                <input type="email" name="alterEmail" id="alterEmail" required value="<?= htmlspecialchars($_SESSION['email']); ?>"><br>

                <label for="alterSenha">Nova senha: </label>
                <input type="password" name="alterSenha" id="alterSenha"><br>
                <button class="salvar" type="submit">Salvar</button>
            </form>
        </div>
    </main>
    <script src="../../../js/script.js"></script>
    <script src="../../../js/alert.js"></script>
</body>

</html>

<?php

function validarEntrada($data)
{
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = validarEntrada($_POST['alterNome']);
    $sobrenome = validarEntrada($_POST['alterSobrenome']);
    $email = filter_var(validarEntrada($_POST['alterEmail']), FILTER_VALIDATE_EMAIL);
    $senha = validarEntrada($_POST['alterSenha']);

    if ($nome && $sobrenome && $email) {
        try {
            $dbh = Conexao::getConexao();
            $sql = "UPDATE caopanheiro.administrador SET Nome = :nome, Sobrenome = :sobrenome, Email = :email WHERE adminId = :id";

            if (!empty($senha)) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE caopanheiro.administrador SET Nome = :nome, Sobrenome = :sobrenome, Email = :email, Senha = :senha WHERE adminId = :id";
            }

            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $_SESSION['usuId']);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':sobrenome', $sobrenome);
            $stmt->bindParam(':email', $email);

            if (!empty($senha)) {
                $stmt->bindParam(':senha', $senhaHash);
            }

            if ($stmt->execute()) {
                $_SESSION['nome'] = $nome;
                $_SESSION['sobrenome'] = $sobrenome;
                $_SESSION['email'] = $email;
                header("Location: alterarAdmin.php?status=success");
                exit();
            } else {
                echo "Erro ao alterar os dados.";
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    } else {
        echo "Todas as informações devem estar preenchidas!";
    }
}

ob_end_flush();
?>