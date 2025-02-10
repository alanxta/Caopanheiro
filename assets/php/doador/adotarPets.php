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

$query = "SELECT * FROM caopanheiro.pets WHERE doador = :doador AND status = 'disponivel'";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':doador', $_SESSION['usuId'], PDO::PARAM_INT);
$stmt->execute();
$quantidadeRegistros = $stmt->rowCount();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar adoção</title>
    <link rel="stylesheet" href="../../css/dashboards.css">
    <style>
        body {
            background-color: var(--color2);
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="pets">
        <h1>Qual de seus pets deseja doar?</h1>
        <?php if ($quantidadeRegistros == 0) : ?>
            <p>Não existem pets disponiveis</p><a onclick="history.go(-1)">Voltar</a>
        <?php else : ?>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                <a href="finalizarAdocao.php?petId=<?= htmlspecialchars($row['petId'], ENT_QUOTES, 'UTF-8'); ?>&adotante=<?= $adotanteId ?>">
                    <p><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?></p>
                </a>
                <p><?= htmlspecialchars($row['raca'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endwhile; ?>
        <?php endif; ?>
        <?php $dbh = null; ?>
    </div>
</body>

</html>
