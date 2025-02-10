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

$adotanteId = filter_input(INPUT_GET, 'adotante', FILTER_SANITIZE_NUMBER_INT);
$petId = filter_input(INPUT_GET, 'petId', FILTER_SANITIZE_NUMBER_INT);

if ($adotanteId && $petId) {
    $query = "UPDATE caopanheiro.pets SET status='adotado' WHERE petId = :petId";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':petId', $petId, PDO::PARAM_INT);
    $result = $stmt->execute();

    if ($result) {
        $query = "INSERT INTO caopanheiro.adocao (adotante, petId, dataAdocao) VALUES (:adotante, :petId, NOW())";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':adotante', $adotanteId, PDO::PARAM_INT);
        $stmt->bindParam(':petId', $petId, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>alert('Pet doado com sucesso');</script>";
            echo "<script>window.location.href='pets.php';</script>";
        } else {
            echo "Não foi possível doar esse pet.";
        }
    } else {
        echo "Pet não encontrado.";
        header("Location: ../chat/chat.php");
    }
} else {
    echo "Dados inválidos.";
    header("Location: ../chat/chat.php");
}
