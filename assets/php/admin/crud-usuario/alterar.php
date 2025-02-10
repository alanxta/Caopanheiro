<?php
ob_start();
session_start();
require __DIR__ . '/../../conexao.php';
$dbh = Conexao::getConexao();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber dados do formulário
    $usuId = $_POST['usuId'];
    $nome = $_POST['alterNome'];
    $sobrenome = $_POST['alterSobrenome'];
    $dataNasc = $_POST['alterDataNasc'];
    $endereco = $_POST['alterEndereco'];
    $email = $_POST['alterEmail'];
    $senha = $_POST['alterSenha'];

    // Verificar se todos os campos estão preenchidos
    if (!empty($nome) && !empty($sobrenome) && !empty($dataNasc) && !empty($endereco) && !empty($email)) {
        // Atualizar os dados do usuário
        $sql = "UPDATE caopanheiro.usuario SET nome = :nome, sobrenome = :sobrenome, data_nascimento = :dataNasc, endereco = :endereco, Email = :email";

        // Se uma nova senha foi fornecida, atualizar também a senha
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql .= ", senha = :senha";
        }

        $sql .= " WHERE usuarioId = :id";

        // Preparar a consulta
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $usuId);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':sobrenome', $sobrenome);
        $stmt->bindParam(':dataNasc', $dataNasc);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':email', $email);

        // Se uma nova senha foi fornecida, vinculá-la à consulta
        if (!empty($senha)) {
            $stmt->bindParam(':senha', $senhaHash);
        }

        // Executar a consulta
        $result = $stmt->execute();

        if ($result) {
            // Redirecionar para a mesma página com uma mensagem de sucesso
            header("Location: alterarUsuarios.php?status=success&Id=$usuId");
            exit();
        } else {
            echo "Erro ao alterar os dados";
        }
    } else {
        echo "Todas as informações devem estar preenchidas!";
    }
}
ob_end_flush();
