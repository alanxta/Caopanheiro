<?php
session_start();
require __DIR__ . '/../../conexao.php';
$dbh = Conexao::getConexao();

# cria o comando UPDATE filtrado pelo campo id
$query = "UPDATE caopanheiro.administrador SET status = 'inativo' where adminId = :id";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $_SESSION['usuId']);
$stmt->execute();

if ($stmt->rowCount() == 1) {
    echo "<script>window.alert('Excluido com sucesso')</script>";
    header('location: ../../../../index.php');
    session_destroy();
    exit();
} else {
    echo "<script>window.alert('Erro ao excluir usuário')</script>";
    echo "<script>window.location.href = '../administrador_dashboard.php'</script>";
}
$dbh = null;
