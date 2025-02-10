<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

require_once 'conexao.php';

function validarEntrada($data)
{
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validarCPF($cpf)
{
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Calcula o primeiro dígito verificador
    for ($i = 0, $soma1 = 0; $i < 9; $i++) {
        $soma1 += $cpf[$i] * (10 - $i);
    }
    $resto1 = $soma1 % 11;
    $dv1 = $resto1 < 2 ? 0 : 11 - $resto1;

    // Calcula o segundo dígito verificador
    for ($i = 0, $soma2 = 0; $i < 10; $i++) {
        $soma2 += $cpf[$i] * (11 - $i);
    }
    $resto2 = $soma2 % 11;
    $dv2 = $resto2 < 2 ? 0 : 11 - $resto2;

    // Verifica se os dígitos verificadores são válidos
    if ($cpf[9] != $dv1 || $cpf[10] != $dv2) {
        return false;
    }

    return true;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = validarEntrada($_POST['nome']);
    $sobrenome = validarEntrada($_POST['sobrenome']);
    $datanasc = validarEntrada($_POST['dataNasc']);
    $cpf = validarEntrada($_POST['cpf']);
    $endereco = validarEntrada($_POST['endereco']);
    $cidade = validarEntrada($_POST['cidade']);
    $estado = validarEntrada($_POST['estado']);
    $complemento = validarEntrada($_POST['complemento']);
    $enderecoCompleto = $endereco . ' ' . $complemento;
    $email = filter_var(validarEntrada($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = password_hash(validarEntrada($_POST['senha']), PASSWORD_DEFAULT);
    $perfil = validarEntrada($_POST['perfil']);

    if (empty($nome) || empty($sobrenome) || empty($datanasc) || empty($cpf) || empty($endereco) || empty($cidade) || empty($estado) || empty($email) || empty($senha) || empty($perfil)) {
        $_SESSION['mensagem'] = 'Por favor, preencha todos os campos obrigatórios.';
        header('Location: cadastro.php');
        exit;
    }

    if (!validarCPF($cpf)) {
        echo "<script>
    alert('CPF Inválido');
    history.go(-1);
    
    </script>";
        $_SESSION['mensagem'] = 'CPF inválido.';
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email Inválido')</script>";
        $_SESSION['mensagem'] = 'E-mail inválido.';
        echo "<script>window.alert.href = cadastro.php</script>";
        exit;
    }

    try {
        $dbh = Conexao::getConexao();

        $query = "INSERT INTO caopanheiro.usuario (nome, sobrenome, data_nascimento, cpf, cidade, estado, endereco, email, senha, perfil) 
                  VALUES (:nome, :sobrenome, :datanasc, :cpf, :cidade, :estado, :endereco, :email, :senha, :perfil)";

        $stmt = $dbh->prepare($query);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':sobrenome', $sobrenome);
        $stmt->bindParam(':datanasc', $datanasc);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':cidade', $cidade);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':endereco', $enderecoCompleto);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':perfil', $perfil);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = 'Cadastrado com Sucesso!';
            echo "<script>
                alert('Cadastrado com Sucesso!');
                window.location.href='Paglogin.php';
            </script>";
            exit;
        } else {
            echo "<script>alert('Não foi possivel inserir Usuário!')</script>";
            $_SESSION['mensagem'] = 'Não foi possível inserir Usuário!';
            $error = $stmt->errorInfo();
            print_r($error);
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = 'Erro ao conectar-se ao banco de dados: ' . $e->getMessage();
        header('Location: cadastro.php');
        exit;
    } finally {
        $dbh = null;
    }

    header('Location: cadastro.php');
    exit;
} else {
    echo "ERRO !";
    $_SESSION['mensagem'] = 'Método de requisição inválido.';
    header('Location: cadastro.php');
    exit;
}
