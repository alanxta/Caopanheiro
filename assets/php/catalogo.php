<?php
session_start();
require_once 'conexao.php';

$dbh = Conexao::getConexao();

// Verificar se o usuário está logado e obter seu perfil
$perfilUsuario = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';

// Configuração da paginação
$itemsPorPagina = 4;
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$offset = ($pagina - 1) * $itemsPorPagina;

// Parâmetros de filtro
$sexo = isset($_GET['sexo']) ? $_GET['sexo'] : '';

// Pesquisa
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Construção da consulta SQL com base nos filtros e pesquisa
$query = "SELECT * FROM caopanheiro.pets WHERE 1=1 AND status = 'disponivel'";
$params = array();

if (!empty($sexo)) {
    $query .= " AND sexo = ?";
    $params[] = $sexo;
}

if (!empty($pesquisa)) {
    $query .= " AND (nome LIKE ? OR raca LIKE ? OR descricao LIKE ?)";
    $params = array_merge($params, array("%$pesquisa%", "%$pesquisa%", "%$pesquisa%"));
}

$query .= " LIMIT $itemsPorPagina OFFSET $offset";

$stmt = $dbh->prepare($query);
$stmt->execute($params);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contagem total de registros para a paginação
$totalQuery = "SELECT COUNT(*) AS total FROM caopanheiro.pets WHERE 1=1 AND status = 'disponivel'";

if (!empty($sexo)) {
    $totalQuery .= " AND sexo = ?";
}

if (!empty($pesquisa)) {
    $totalQuery .= " AND (nome LIKE ? OR raca LIKE ? OR descricao LIKE ?)";
}

$totalStmt = $dbh->prepare($totalQuery);
$totalStmt->execute($params);
$totalPets = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPaginas = ceil($totalPets / $itemsPorPagina);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pets</title>
    <link rel="stylesheet" href="../css/catalogo.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <header>
        <?php if (
           isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'adotante') : ?>
            <span class="material-symbols-outlined" onclick="window.location.href='adotante/adotante_dashboard.php'">
                arrow_back
            </span>
        <?php else : ?>
            <span class="material-symbols-outlined" onclick="window.location.href='../../index.php'">
                arrow_back
            </span>
        <?php endif; ?>

        <h1>Lista de Pets</h1>
        <form action="" method="get">
            <input type="text" name="pesquisa" placeholder="Pesquisar">
            <select name="sexo">
                <option value="">Sexo</option>
                <option value="M">Macho</option>
                <option value="F">Fêmea</option>
            </select>
            <button type="submit">Filtrar</button>
        </form>
    </header>



    <div class="container">
        <?php foreach ($pets as $pet) : ?>
            <div class="pet-card">
                <img src="<?= htmlspecialchars($pet['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto do Pet">
                <div class="pet-info">
                    <h2><?= htmlspecialchars($pet['nome'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><strong>Raça:</strong> <?= htmlspecialchars($pet['raca'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Sexo:</strong> <?= htmlspecialchars($pet['sexo'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong><?= htmlspecialchars($pet['status'] == 'adotado' ? 'Adotado' : 'Disponível', ENT_QUOTES, 'UTF-8'); ?></strong> </p>
                    <p style="display:none;"><strong>Doador:</strong> <?= htmlspecialchars($pet['doador'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($pet['descricao'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <button class="btn-chat"><a onclick="VerificarLogin()" href="<?php echo $perfilUsuario === 'adotante' ? 'chat/create_chat.php?destinatario=' . htmlspecialchars($pet['doador'], ENT_QUOTES, 'UTF-8') : 'chat/listaChats.php'; ?>">Chat</a></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <div class="pagination">
        <?php if ($pagina > 1) : ?>
            <a href="?pagina=<?= ($pagina - 1) ?>">Anterior</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
            <a href="?pagina=<?= $i ?>" <?= $pagina == $i ? 'class="active"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($pagina < $totalPaginas) : ?>
            <a href="?pagina=<?= ($pagina + 1) ?>">Próxima</a>
        <?php endif; ?>
    </div>

    <script>
        function VerificarLogin() {
            // Verificar se o usuário está logado antes de acessar o chat
            if (!<?php echo isset($_SESSION['usuId']) ? 'true' : 'false'; ?>) {
                event.preventDefault();
                let confirmation = confirm('É preciso estar logado para acessar o chat. Redirecionar?');

                if (confirmation) {
                    alert('Redirecionando...');
                    window.location.href = 'Paglogin.php';
                }
            }
        }
    </script>

</body>

</html>