<?php
session_start();
require __DIR__ . '/../conexao.php';

try {
    $dbh = Conexao::getConexao();

    // Definindo variáveis para paginação
    $porPagina = 5; // Número de registros por página
    $paginaAtual = isset($_GET['pagina']) ? $_GET['pagina'] : 1; // Página atual

    // Definindo variáveis para os filtros e pesquisa
    $filtroStatus = isset($_GET['status']) ? $_GET['status'] : '';
    $filtroPesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

    // Construindo a query base
    $query = "SELECT * FROM caopanheiro.administrador WHERE 1";

    // Adicionando filtros
    if (!empty($filtroStatus)) {
        $query .= " AND status = :status";
    }
    if (!empty($filtroPesquisa)) {
        $query .= " AND (nome LIKE :pesquisa OR sobrenome LIKE :pesquisa OR email LIKE :pesquisa)";
    }

    // Preparando a query com os filtros
    $stmt = $dbh->prepare($query);

    // Adicionando valores dos filtros
    if (!empty($filtroStatus)) {
        $stmt->bindValue(':status', $filtroStatus, PDO::PARAM_STR);
    }
    if (!empty($filtroPesquisa)) {
        $stmt->bindValue(':pesquisa', '%' . $filtroPesquisa . '%', PDO::PARAM_STR);
    }

    $stmt->execute();

    // Calculando total de registros e páginas
    $totalRegistros = $stmt->rowCount();
    $totalPaginas = ceil($totalRegistros / $porPagina);

    // Calculando o offset para a página atual
    $offset = ($paginaAtual - 1) * $porPagina;

    // Construindo a query com limit e offset
    $query .= " LIMIT $porPagina OFFSET $offset";

    // Executando a query final
    $stmt = $dbh->prepare($query);
    if (!empty($filtroStatus)) {
        $stmt->bindValue(':status', $filtroStatus, PDO::PARAM_STR);
    }
    if (!empty($filtroPesquisa)) {
        $stmt->bindValue(':pesquisa', '%' . $filtroPesquisa . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Restrita</title>
    <link rel="stylesheet" href="../../css/dashboards.css">
    <link rel="stylesheet" href="../../css/filtro.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        #admin {
            width: 150px;
        }

        .acoes a {
            text-decoration: none;
            color: white;
        }
        nav{
            height: 84vh;
        }
        div.content {
            height: 84%;
        }
        .email{
            width: 20%;
        }
    </style>
</head>

<body>
    <header>
        <button class="nav-toggle"><span class="material-symbols-outlined">menu</span></button>
        <figure class="logo"><img src="../../img/logo1.png" alt=""></figure>
        <div class="user-info">Bem-vindo,
            <?= htmlspecialchars($_SESSION['nome'] ?? 'Visitante', ENT_QUOTES, 'UTF-8'); ?> <span id="username"></span>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="crud-pet/pets.php">Pets cadastrados</a></li>
            <li><a href="crud-usuario/listaUsuarios.php">Usuários cadastrados</a></li>
            <li><a href="administrador_dashboard.php">Meu Perfil</a></li>
            <li><a href="listaAdmin.php">Administradores</a></li>
            <li><a href="../logout.php">Sair</a></li>
        </ul>
    </nav>
    <main>
        <div class="content" id="conteudo">
            <h1>Administradores cadastrados</h1>
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
                <input type="text" name="pesquisa" id="pesquisa" value="<?= $filtroPesquisa ?>" placeholder="Digite aqui...">
                <button type="submit">Filtrar</button>
            </form>
            <section>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th id="nome">Nome</th>
                            <th>Sobrenome</th>
                            <th class="email">Email</th>
                            <th>Perfil</th>
                            <th>Status</th>
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
                                    <td><?= intval($row['adminId']); ?></td>
                                    <td><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['sobrenome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="email"><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['perfil'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= ($row['status'] == 'ativo') ? 'ATIVO' : 'INATIVO'; ?></td>
                                    <td class="acoes">
                                        <button class="acoes"><a class="btnalterar" href="crud-admin/alterarAdministradores.php?Id=<?= intval($row['adminId']); ?>">Alterar</a></button>
                                        <?php if ($row['status'] == 'ativo') : ?>
                                            <button class='acoes'><a class='btnexcluir' href='crud-admin/excluirAdministradores.php?Id=<?= intval($row['adminId']); ?>' onclick='return confirm("Deseja confirmar a operação?");'>Excluir</a></button>
                                        <?php else : ?>
                                            <button class='acoes'><a class='btnexcluir' href='crud-admin/reativarAdmin.php?Id=<?= intval($row['adminId']); ?>' onclick='return confirm("Deseja confirmar a operação?");'>Reativar</a></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        <?php $dbh = null; ?>
                    </tbody>
                </table>
            </section>
            <!-- Paginação -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
                    <a href="?pagina=<?= $i ?>&status=<?= $filtroStatus ?>&perfil=<?= $filtroPerfil ?>&pesquisa=<?= $filtroPesquisa ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <button id="admin"><a href="crud-admin/newAdmin.php">Novo Administador</a></button>
        </div>
    </main>
    <script src="../../js/script.js"></script>
</body>

</html>