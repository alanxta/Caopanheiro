<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['nome'])) {
    header("Location: ../../login.php");
    exit;
}

require __DIR__ . '/../../conexao.php';

try {
    $dbh = Conexao::getConexao();

    // Definindo variáveis para paginação
    $porPagina = 4; // Número de registros por página
    $paginaAtual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1; // Página atual
    if ($paginaAtual < 1) $paginaAtual = 1;

    // Definindo variáveis para os filtros e pesquisa
    $filtroStatus = isset($_GET['status']) ? $_GET['status'] : '';
    $filtroPesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

    // Construindo a query base para contagem
    $queryCount = "SELECT COUNT(*) FROM caopanheiro.usuario WHERE 1";

    // Adicionando filtros na query de contagem
    if (!empty($filtroStatus)) {
        $queryCount .= " AND status = :status";
    }
    if (!empty($filtroPesquisa)) {
        $queryCount .= " AND (nome LIKE :pesquisa OR sobrenome LIKE :pesquisa OR email LIKE :pesquisa)";
    }

    // Preparando a query de contagem
    $stmtCount = $dbh->prepare($queryCount);

    // Adicionando valores dos filtros na query de contagem
    if (!empty($filtroStatus)) {
        $stmtCount->bindValue(':status', $filtroStatus, PDO::PARAM_STR);
    }
    if (!empty($filtroPesquisa)) {
        $stmtCount->bindValue(':pesquisa', '%' . $filtroPesquisa . '%', PDO::PARAM_STR);
    }
    $stmtCount->execute();
    $totalRegistros = $stmtCount->fetchColumn();
    $totalPaginas = ceil($totalRegistros / $porPagina);

    // Calculando o offset para a página atual
    $offset = ($paginaAtual - 1) * $porPagina;

    // Construindo a query final com filtros, limit e offset
    $query = "SELECT * FROM caopanheiro.usuario WHERE 1";
    if (!empty($filtroStatus)) {
        $query .= " AND status = :status";
    }
    if (!empty($filtroPesquisa)) {
        $query .= " AND (nome LIKE :pesquisa OR sobrenome LIKE :pesquisa OR email LIKE :pesquisa)";
    }
    $query .= " LIMIT :limit OFFSET :offset";

    // Preparando a query final
    $stmt = $dbh->prepare($query);
    if (!empty($filtroStatus)) {
        $stmt->bindValue(':status', $filtroStatus, PDO::PARAM_STR);
    }
    if (!empty($filtroPesquisa)) {
        $stmt->bindValue(':pesquisa', '%' . $filtroPesquisa . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    echo "Erro na conexão: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    die();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Restrita</title>
    <link rel="stylesheet" href="../../../css/dashboards.css">
    <link rel="stylesheet" href="../../../css/filtro.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        nav{
            height: 84vh;
        }
        div.content {
            height: 84%;
        }

        .id{
            width: 2%;
        }
        .sobrenome,.nome,.data{
            width: 10%;
        }
        .perfil,.status{
            width: 8%;
        }
        td,th{
            width: 20%;
        }
        td{
            height: 65px;
            border-bottom: 1px solid var(--color3);
        }
        div.pagination {
            text-align: center;
            margin-bottom: 10px;
        }
        a#pag {
            display: inline-block;
            width: 15px;
            color: var(--color1);
        }

    </style>
</head>

<body>
    <header>
        <button class="nav-toggle"><span class="material-symbols-outlined">menu</span></button>
        <figure class="logo"><img src="../../../img/logo1.png" alt=""></figure>
        <div class="user-info">Bem-vindo, <?= htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?> <span id="username"></span></div>
    </header>
    <nav>
        <ul>
            <li><a href="../crud-pet/pets.php">Pets cadastrados</a></li>
            <li><a href="listaUsuarios.php">Usuários cadastrados</a></li>
            <li><a href="../administrador_dashboard.php">Meu Perfil</a></li>
            <li><a href="../listaAdmin.php">Administradores</a></li>
            <li><a href="../../logout.php">Sair</a></li>
        </ul>
    </nav>
    <main>
        <div class="content" id="conteudo">
            <h1>Usuários cadastrados</h1>
            <hr>
            <!-- Formulário de filtro e pesquisa -->
            <form action="" method="GET">
                <label for="status">Filtrar por status:</label>
                <select name="status" id="status">
                    <option value="">Todos</option>
                    <option value="ativo" <?php if ($filtroStatus == 'ativo') echo 'selected'; ?>>Ativo</option>
                    <option value="inativo" <?php if ($filtroStatus == 'inativo') echo 'selected'; ?>>Inativo</option>
                </select>
                <label for="pesquisa">Pesquisar:</label>
                <input type="text" name="pesquisa" id="pesquisa" value="<?= htmlspecialchars($filtroPesquisa, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Digite aqui...">
                <button type="submit">Filtrar</button>
            </form>
            <section>
                <table id="pet">
                    <thead>
                        <tr>
                            <th class="id">ID</th>
                            <th class="nome">Nome</th>
                            <th class="sobrenome">Sobrenome</th>
                            <th class="data">Data de Nascimento</th>
                            <th>Endereço</th>
                            <th>Email</th>
                            <th class="perfil">Perfil</th>
                            <th class="status">Status</th>
                            <th colspan="2" class="acoes">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalRegistros == 0) : ?>
                            <tr>
                                <td colspan="9">Não existem usuários cadastrados.</td>
                            </tr>
                        <?php else : ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                                <tr>
                                    <td class="id"><?= intval($row['usuarioId']); ?></td>
                                    <td class="nome"><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="sobrenome"><?= htmlspecialchars($row['sobrenome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="data"><?= htmlspecialchars($row['data_nascimento'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['endereco'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="perfil"><?= htmlspecialchars($row['perfil'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="status"><?= ($row['status'] == 'ativo') ? 'ATIVO' : 'INATIVO'; ?></td>
                                    <td class="acoes">
                                        <button class="acoes"><a class="btnalterar" href="alterarUsuarios.php?Id=<?= intval($row['usuarioId']); ?>">Alterar</a></button>
                                        <?php if ($row['status'] == 'ativo') : ?>
                                            <button class="acoes"><a class="btnexcluir" href="excluirUsuarios.php?Id=<?= intval($row['usuarioId']); ?>" onclick="return confirm('Deseja confirmar a operação?');">Excluir</a></button>
                                        <?php else : ?>
                                            <button class="acoes"><a class="btnexcluir" href="reativarUsuario.php?Id=<?= intval($row['usuarioId']); ?>" onclick="return confirm('Deseja confirmar a operação?');">Reativar</a></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
            <!-- Paginação -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
                    <a id="pag" href="?pagina=<?= $i ?>&status=<?= urlencode($filtroStatus) ?>&pesquisa=<?= urlencode($filtroPesquisa) ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </main>
    <script src="../../../js/script.js"></script>
</body>
</html>
