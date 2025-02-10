<?php
session_start();
require __DIR__ . '/../conexao.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuId'])) {
    header("Location: ../login.php"); // Redireciona para a página de login se não estiver autenticado
    exit();
}

$dbh = Conexao::getConexao();

if (!$dbh) {
    die("Erro ao conectar ao banco de dados.");
}

$chatId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$destinatario = filter_input(INPUT_GET, 'destinatario', FILTER_SANITIZE_SPECIAL_CHARS);
$usuarioId = $_SESSION['usuId'];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/chat.css">
    <title>Chat</title>
</head>

<body>
    <button id="volta"><a onclick="history.go(-1)">Voltar</a></button>
    <div id="chat">
        <h2>Chat</h2>
        <div id="messages">
            <!-- Mensagens serão carregadas aqui -->
        </div>
        <form id="sendMessageForm">
            <input type="hidden" name="destinatario" id="destinatario" value="<?= htmlspecialchars($destinatario, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="chatId" id="chatId" value="<?= htmlspecialchars($chatId, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="usuarioId" id="usuarioId" value="<?= htmlspecialchars($usuarioId, ENT_QUOTES, 'UTF-8') ?>">
            <textarea name="conteudo" id="conteudo" rows="3" required></textarea>
            <button type="submit">Enviar</button>
        </form>
    </div>
    <?php
    if ($_SESSION['perfil'] == 'doador') {
        echo "<div id='adocao'><a href='../doador/adotarPets.php?adotante=$destinatario' onclick=\"return confirm('Deseja doar um pet para esse usuario?');\">Finalizar adoção</a></div>";
    }

    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const destinatario = document.getElementById('destinatario').value;
            const usuarioId = document.getElementById('usuarioId').value;
            const messagesDiv = document.getElementById('messages');
            const sendMessageForm = document.getElementById('sendMessageForm');
            const conteudo = document.getElementById('conteudo');

            const formatDate = (dateString) => {
                const date = new Date(dateString);
                const month = ('0' + (date.getMonth() + 1)).slice(-2);
                const day = ('0' + date.getDate()).slice(-2);
                const hours = ('0' + date.getHours()).slice(-2);
                const minutes = ('0' + date.getMinutes()).slice(-2);
                return `${day}/${month} ${hours}:${minutes}`;
            };

            const loadMessages = async () => {
                try {
                    const response = await fetch(`receberMensagens.php?destinatario=${encodeURIComponent(destinatario)}`);
                    const data = await response.json();
                    if (!Array.isArray(data)) {
                        console.error('Resposta inválida:', data);
                        return;
                    }
                    messagesDiv.innerHTML = '';
                    data.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message');
                        messageDiv.classList.add(message.remetente == usuarioId ? 'sent' : 'received');
                        messageDiv.innerHTML = `<strong>${message.RemetenteNome}:</strong> <div class="text">${message.conteudo}<br/> <div class="hora">${formatDate(message.dataEnvio)}</div></div>`;
                        messagesDiv.appendChild(messageDiv);
                    });
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                } catch (error) {
                    console.error('Erro ao carregar mensagens:', error);
                }
            };

            sendMessageForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(sendMessageForm);
                try {
                    const response = await fetch('enviarMensagem.php', {
                        method: 'POST',
                        body: formData
                    });
                    if (response.ok) {
                        conteudo.value = '';
                        loadMessages();
                    } else {
                        console.error('Erro ao enviar mensagem.');
                    }
                } catch (error) {
                    console.error('Erro ao enviar mensagem:', error);
                }
            });

            loadMessages();
            setInterval(loadMessages, 10000);
        });
    </script>

</body>

</html>