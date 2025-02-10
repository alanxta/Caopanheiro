<?php
session_start();
// Conexão com o banco de dados MySQL usando PDO
require_once 'conexao.php';

$dbh = Conexao::getConexao();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email= $_GET['email'];
    $novaSenha = password_hash($_POST['novaSenha'], PASSWORD_DEFAULT);

    try {
        $sql = "UPDATE caopanheiro.usuario SET senha = :senha WHERE email = :email";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":senha", $novaSenha);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Sua senha foi atualizada com sucesso.";
            header("Location: Paglogin.php");
        } else {
            echo "Erro ao atualizar a senha. Verifique o email informado.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    header("Location: redefinirSenha.php");
    exit();
}
?>