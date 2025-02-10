<?php
session_start();
require __DIR__ . '/../conexao.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuId'])) {
    header("Location: ../login.php"); // Redireciona para a página de login se não estiver autenticado
    exit();
}

$dbh = Conexao::getConexao();

// Consulta para obter os chats associados ao usuário e ordená-los pela última mensagem
$stmt = $dbh->prepare("SELECT c.chatId, 
                              CASE WHEN c.doador = :usuarioId THEN (SELECT nome FROM usuario WHERE usuarioId = c.Adotante)
                                   ELSE (SELECT nome FROM usuario WHERE usuarioId = c.doador)
                              END AS nomeDestinatario,
                              CASE WHEN c.doador = :usuarioId THEN c.adotante ELSE c.doador END AS destinatarioID
                      FROM chats c
                      LEFT JOIN mensagens m ON c.chatId = m.chatId
                      WHERE (c.doador = :usuarioId OR c.adotante = :usuarioId)
                      GROUP BY c.chatId
                      ORDER BY MAX(m.dataEnvio) DESC");

$stmt->bindParam(':usuarioId', $_SESSION['usuId'], PDO::PARAM_INT);
$stmt->execute();

$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        div.chat{
            height: 30px;
            width: 100%;
            display: flex;
            align-items:center;
        }
        span.chat,li.chat, li.chat>a{height: 30px;
        color:black;
        }

        li.chat {
            font-size: 1.25em;
            list-style-type: none;
            display: inline-flex;
        }

        li.chat:hover{
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
        <button class="nav-toggle"><span class="material-symbols-outlined">
                menu
            </span></button>
        <figure class="logo"><img src="../../img/logo1.png" alt=""></figure>
        <div class="user-info">Bem-vindo, <?= htmlspecialchars($_SESSION['nome']); ?> <span id="username"></span></div>
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
            <h1>Meus Chats</h1>
            <ul>
                <?php foreach ($chats as $chat) : ?>
                    <div class="chat">
                        <span class="material-symbols-outlined chat">
                        chevron_right
                        </span>
                    <li class="chat">
                        
                        <a href="../chat/chat.php?id=<?= htmlspecialchars($chat['chatId']); ?>&destinatario=<?= htmlspecialchars($chat['destinatarioID']); ?>">
                            <?= htmlspecialchars($chat['nomeDestinatario']); ?>
                            
                        </a>
                    </li></div>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
    <script src="../../js/script.js"></script>
</body>

</html>