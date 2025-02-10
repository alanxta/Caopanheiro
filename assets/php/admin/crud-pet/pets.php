<?php
session_start();

require __DIR__ . '/../../conexao.php';
# solicita a conexão com o banco de dados e guarda na váriavel dbh.
$dbh = Conexao::getConexao();

# Definindo variáveis para paginação
$porPagina = 10; // Número de registros por página
$paginaAtual = isset($_GET['pagina']) ? $_GET['pagina'] : 1; // Página atual

# Definindo variáveis para os filtros e pesquisa
$filtroStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filtroSexo = isset($_GET['sexo']) ? $_GET['sexo'] : '';
$filtroPesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

# Cria uma instrução SQL para selecionar todos os dados na tabela pet.
$query = "SELECT * FROM caopanheiro.pets WHERE 1";

# Adicionando filtros
if (!empty($filtroStatus)) {
    $query .= " AND status = :status";
}
if (!empty($filtroSexo)) {
    $query .= " AND sexo = :sexo";
}
if (!empty($filtroPesquisa)) {
    $query .= " AND (doador LIKE :pesquisa OR nome LIKE :pesquisa OR raca LIKE :pesquisa)";
}

# Preparando a execução da query e retornando para uma variável chamada stmt.
$stmt = $dbh->prepare($query);

# Adicionando valores dos filtros
if (!empty($filtroStatus)) {
    $stmt->bindValue(':status', $filtroStatus, PDO::PARAM_STR);
}
if (!empty($filtroSexo)) {
    $stmt->bindValue(':sexo', $filtroSexo, PDO::PARAM_STR);
}
if (!empty($filtroPesquisa)) {
    $stmt->bindValue(':pesquisa', '%' . $filtroPesquisa . '%', PDO::PARAM_STR);
}

$stmt->execute();

# Devolve a quantidade de linhas retornada pela consulta a tabela.
$quantidadeRegistros = $stmt->rowCount();

# Cria um array para armazenar os IDs dos pets.
$petIds = [];
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
        <div class="user-info">Bem-vindo,
            <?= htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?> <span id="username"></span>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="pets.php">Pets cadastrados</a></li>
            <li><a href="../crud-usuario/listaUsuarios.php">Usuários cadastrados</a></li>
            <li><a href="../administrador_dashboard.php">Meu Perfil</a></li>
            <li><a href="../listaAdmin.php">Administradores</a></li>
            <li><a href="../../logout.php">Sair</a></li>
        </ul>
    </nav>
    <div class="content" id="conteudo">
        <h1>Pets cadastrados</h1>
        </section>

        <hr>

        <!-- Formulário de filtro e pesquisa -->
        <form action="" method="GET">
            <label for="status">Filtrar por status:</label>
            <select name="status" id="status">
                <option value="">Todos</option>
                <option value="adotado" <?php if ($filtroStatus == 'adotado') echo 'selected'; ?>>Adotado</option>
                <option value="disponivel" <?php if ($filtroStatus == 'disponivel') echo 'selected'; ?>>Disponível</option>
            </select>
            <label for="sexo">Filtrar por sexo:</label>
            <select name="sexo" id="sexo">
                <option value="">Todos</option>
                <option value="M" <?php if ($filtroSexo == 'macho') echo 'selected'; ?>>Macho</option>
                <option value="F" <?php if ($filtroSexo == 'femea') echo 'selected'; ?>>Fêmea</option>
            </select>
            <label for="pesquisa">Pesquisar:</label>
            <input type="text" name="pesquisa" id="pesquisa" value="<?= $filtroPesquisa ?>" placeholder="Digite aqui...">
            <button type="submit">Filtrar</button>
        </form>

        <section>
            <table id="pet">
                <thead>
                    <tr>
                        <th>Doador</th>
                        <th id="nome">Nome</th>
                        <th id="raca">Raça</th>
                        <th>Sexo</th>
                        <th>Status</th>
                        <th colspan="2" class="acoes">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($quantidadeRegistros == 0) : ?>
                        <tr>
                            <td colspan="6">Não existem pets cadastrados.</td>
                        </tr>
                    <?php else : ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                            <?php
                            $status = $row['status'] == "adotado" ? "ADOTADO" : "DISPONÍVEL";
                            $petId = intval($row['petId']);
                            # Armazena o ID no array, se ainda não estiver presente
                            if (!in_array($petId, $petIds)) {
                                $petIds[] = $petId;
                            }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['doador'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['raca'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['sexo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $status; ?></td>
                                <td class="acoes">
                                    <button class="acoes">
                                        <a class="btnalterar" href="alterarPet.php?id=<?= $petId; ?>">Alterar</a>
                                    </button>
                                    <button class="acoes">
                                        <a class="btnexcluir" href="excluirPet.php?id=<?= $petId; ?>" onclick="return confirm('Deseja confirmar a operação?');">Excluir</a>
                                    </button>
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
            <?php
            # Calcula o total de páginas
            $totalPaginas = ceil(count($petIds) / $porPagina);

            # Exibe os links para navegação entre páginas
            for ($i = 1; $i <= $totalPaginas; $i++) :
            ?>
                <a id="pag" href="?pagina=<?= $i ?>&status=<?= $filtroStatus ?>&sexo=<?= $filtroSexo ?>&pesquisa=<?= $filtroPesquisa ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <script src="../../../js/script.js"></script>
</body>

</html>