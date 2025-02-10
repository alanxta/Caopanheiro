<?php
session_start();
// Conexão com o banco de dados MySQL usando PDO
require_once 'conexao.php';

$dbh = Conexao::getConexao();

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/login.css">
    <style>
        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .formbox {
            width: 300px;
            height: 230px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .formbox h1 {
            margin-bottom: 20px;
        }

        form {
            width: 100%;
        }

        .inputbox {
            margin: 0 0 20px 0;
            width: 100%;
        }

        .inputbox input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
        }

        .inputbox label {
            display: block;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="formbox" id="box">
            <h1>Redefinir Senha</h1>
            <form action="redefinirSenha.php?email=<?=$email?>" method="post" autocomplete="on">
                <div class="inputbox">
                    <input type="password" name="novaSenha" id="novaSenha" required>
                    <label for="novaSenha">Nova Senha</label>
                </div>
                <button class="env" type="submit">Redefinir Senha</button>
            </form>
        </div>
    </div>
</body>

</html>
