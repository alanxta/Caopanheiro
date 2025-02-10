<?php
session_start();
require_once __DIR__ . '/../conexao.php';

$dbh = Conexao::getConexao();

if (!$dbh) {
    die(json_encode(["erro" => "Erro ao conectar ao banco de dados."]));
}

$remetente = $_SESSION['usuId'];
$destinatario = filter_input(INPUT_GET, 'destinatario', FILTER_VALIDATE_INT);

if ($destinatario === false) {
    die(json_encode(["erro" => "Destinatário inválido."]));
}

try {
    $query = "SELECT m.*, u.nome as RemetenteNome FROM mensagens m JOIN usuario u ON m.remetente = u.usuarioId WHERE (m.remetente = :remetente AND m.destinatario = :destinatario) OR (m.remetente = :destinatario AND m.destinatario = :remetente) ORDER BY m.dataEnvio ASC";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':remetente', $remetente, PDO::PARAM_INT);
    $stmt->bindParam(':destinatario', $destinatario, PDO::PARAM_INT);

    $stmt->execute();

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
} catch (PDOException $e) {
    error_log("Erro ao recuperar mensagens: " . $e->getMessage()); // Log do erro
    echo json_encode(["erro" => "Erro ao recuperar mensagens."]);
}

$dbh = null;
?>
