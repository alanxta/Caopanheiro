<?php
session_start();
require_once __DIR__ . '/../conexao.php';

$dbh = Conexao::getConexao();

if (!$dbh) {
    die("Erro ao conectar ao banco de dados.");
}

$remetente = $_SESSION['usuId'];
$destinatario = filter_input(INPUT_POST, 'destinatario', FILTER_VALIDATE_INT);
$conteudo = htmlspecialchars($_POST['conteudo'], ENT_QUOTES, 'UTF-8');
$chatId = filter_input(INPUT_POST, 'chatId', FILTER_VALIDATE_INT);

if ($destinatario === false || $chatId === false) {
    die("Dados inválidos.");
}

try {
    $query = "INSERT INTO mensagens (chatId, remetente, destinatario, conteudo, dataEnvio) VALUES (:chatId, :remetente, :destinatario, :conteudo, NOW())";
    
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':chatId', $chatId, PDO::PARAM_INT);
    $stmt->bindParam(':remetente', $remetente, PDO::PARAM_INT);
    $stmt->bindParam(':destinatario', $destinatario, PDO::PARAM_INT);
    $stmt->bindParam(':conteudo', $conteudo, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "Mensagem enviada com sucesso!";
    } else {
        echo "Erro ao enviar mensagem.";
    }
} catch (PDOException $e) {
    error_log("Erro ao enviar mensagem: " . $e->getMessage()); // Log do erro
    echo "Erro ao enviar mensagem.";
}

$dbh = null;
?>
