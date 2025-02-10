<?php
session_start();
require __DIR__ . '/../conexao.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuId'])) {
    header("Location: ../login.php");
    exit();
}

// Função para validar e sanitizar entradas
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = $_SESSION['usuId'];
    $petNome = sanitizeInput($_POST['petNome']);
    $petNasc = sanitizeInput($_POST['petNasc']);
    $especie = sanitizeInput($_POST['especie']);
    $porte = sanitizeInput($_POST['porte']);
    $raca = sanitizeInput($_POST['raca']);
    $sexo = sanitizeInput($_POST['sexo']);
    $descricao = sanitizeInput($_POST['descricao']);

    if (isset($_FILES['uploadFoto']) && $_FILES['uploadFoto']['error'] === UPLOAD_ERR_OK) {
        $fotoTmpPath = $_FILES['uploadFoto']['tmp_name'];
        $fotoName = basename($_FILES['uploadFoto']['name']);
        $fotoSize = $_FILES['uploadFoto']['size'];
        $fotoType = $_FILES['uploadFoto']['type'];

        $uploadDir = __DIR__ . '/imgPets/';
        $relativePath = 'doador/imgPets/' . $fotoName;
        $destPath = $uploadDir . $fotoName;

        if (move_uploaded_file($fotoTmpPath, $destPath)) {
            try {
                $dbh = Conexao::getConexao();
                $stmt = $dbh->prepare("INSERT INTO pets (doador, nome, dataNascimento, especie, porte, raca, sexo, descricao, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$usuarioId, $petNome, $petNasc, $especie, $porte, $raca, $sexo, $descricao, $relativePath]);
                header("location: formCadastroPet.php?status=success");
            } catch (PDOException $e) {
                echo "Erro ao cadastrar pet: " . $e->getMessage();
            }
        } else {
            echo "Erro ao fazer upload da foto.";
        }
    } else {
        echo "Erro no upload da foto.";
    }
}
?>
