<?php
session_start();
require __DIR__ . '/../../conexao.php';
$dbh = Conexao::getConexao();

if (isset($_GET['Id'])) {
  $usuId = intval($_GET['Id']);

  # cria o comando DELETE filtrado pelo campo id
  $query = "UPDATE caopanheiro.usuario SET status = 'ativo' where usuarioId = :id";

  $stmt = $dbh->prepare($query);
  $stmt->bindParam(':id', $usuId);
  $result = $stmt->execute();

  if ($result) {
    echo "<script>window.alert('Usuario Reativado com sucesso')</script>";
    header("location: listaUsuarios.php");
    exit();
  } else {
    echo "<script>window.alert('Erro ao Reativar usuário')</script>";
    header("location: listaUsuarios.php");
    exit();
  }
} else {

  echo "ID do usuário não fornecido!";
  exit();
}
