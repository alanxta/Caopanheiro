<?php
ob_start();
session_start();
require __DIR__ . '/../../conexao.php';
$dbh = Conexao::getConexao();

// Obtendo o ID do pet
$petId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['petId']) ? intval($_POST['petId']) : 0);

// Verificar se o ID é válido
if ($petId <= 0) {
    die('ID do pet inválido.');
}

// Selecionar os dados do pet
$query = "SELECT * FROM caopanheiro.pets WHERE petId = :id";
$stmt = $dbh->prepare($query);
$stmt->bindValue(':id', $petId, PDO::PARAM_INT);
$stmt->execute();
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $petNome = $_POST['alterNome'];
    $petNascimento = $_POST['alterPetNasc'];
    $petPorte = $_POST['porte'];
    $petRaca = $_POST['raca'];
    $petSexo = $_POST['sexo'];
    $descricao = $_POST['descricao'];

    // Verifica se o upload da foto foi realizado com sucesso
    if (isset($_FILES['uploadFoto']) && $_FILES['uploadFoto']['error'] == UPLOAD_ERR_OK) {
        // Diretório de upload
        $uploadFilePath = __DIR__ . '/imgPets/';
        
        // Verifica e cria o diretório de upload, se necessário
        if (!is_dir($uploadFilePath)) {
            if (!mkdir($uploadFilePath, 0777, true)) {
                die('Erro ao criar o diretório de upload.');
            }
        }

        // Verifica se o diretório de upload possui permissões de escrita
        if (!is_writable($uploadFilePath)) {
            die('O diretório de upload não possui permissões de escrita.');
        }

        // Informações do arquivo enviado
        $fileTmpPath = $_FILES['uploadFoto']['tmp_name'];
        $fileName = $_FILES['uploadFoto']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

        // Verifica a extensão do arquivo
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Gera um novo nome único para o arquivo
            $newFileName = time() . '.' . $fileExtension;
            $dest_path = $uploadFilePath . $newFileName;

            // Move o arquivo para o diretório de upload
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $relativeFilePath = 'imgPets/' . $newFileName;
                $query = "UPDATE caopanheiro.pets SET nome = :nome, dataNascimento = :dataNascimento, raca = :raca, porte = :porte, sexo = :sexo, descricao = :descricao, foto = :foto WHERE petId = :petId";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':nome', $petNome);
                $stmt->bindParam(':dataNascimento', $petNascimento);
                $stmt->bindParam(':raca', $petRaca);
                $stmt->bindParam(':porte', $petPorte);
                $stmt->bindParam(':sexo', $petSexo);
                $stmt->bindParam(':descricao', $descricao);
                $stmt->bindParam(':foto', $relativeFilePath);
                $stmt->bindParam(':petId', $petId, PDO::PARAM_INT);
                $result = $stmt->execute();

                // Redireciona após a operação ser concluída
                if ($result) {
                    header("Location: alterarPet.php?Id=$petId&status=success");
                    exit;
                } else {
                    echo '<p>Não foi possível cadastrar o pet!</p>';
                    $error = $dbh->errorInfo();
                    print_r($error);
                }
            } else {
                echo "Erro ao mover o arquivo para o diretório de upload.";
            }
        } else {
            echo "Tipo de arquivo não permitido.";
        }
    } else {
        // Caso nenhuma imagem seja enviada, atualizar os outros campos
        $query = "UPDATE caopanheiro.pets SET nome = :nome, dataNascimento = :dataNascimento, raca = :raca, porte = :porte, sexo = :sexo, descricao = :descricao WHERE petId = :petId";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':nome', $petNome);
        $stmt->bindParam(':dataNascimento', $petNascimento);
        $stmt->bindParam(':raca', $petRaca);
        $stmt->bindParam(':porte', $petPorte);
        $stmt->bindParam(':sexo', $petSexo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':petId', $petId, PDO::PARAM_INT);
        $result = $stmt->execute();

        // Redireciona após a operação ser concluída
        if ($result) {
            header("Location: alterarPet.php?id=$petId&status=success");
            exit;
        } else {
            echo '<p>Não foi possível cadastrar o pet!</p>';
            $error = $dbh->errorInfo();
            print_r($error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Restrita</title>
    <link rel="stylesheet" href="../../../css/dashboards.css">
    <link rel="stylesheet" href="../../../css/cadastroPet.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        h1 {
            text-align: center;
        }
        nav {
            height: 115% !important;
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
            <li><a href="pets.php">Pets cadastrados</a></li>
            <li><a href="../crud-usuario/listaUsuarios.php">Usuários cadastrados</a></li>
            <li><a href="../administrador_dashboard.php">Meu Perfil</a></li>
            <li><a href="../listaAdmin.php">Administradores</a></li>
            <li><a href="../../logout.php">Sair</a></li>
        </ul>
    </nav>
    <main>
        <div class="content" id="conteudo">
            <h1>Alterar Informações</h1>
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="alert">
                    <span class="closebtn" onclick="this.parentElement.style.display = 'none';">&times;</span>
                    Dados Alterados com sucesso!
                </div>
            <?php endif; ?>
            <form action="alterarPet.php?id=<?= intval($petId) ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="petId" value="<?= intval($petId) ?>">
                <div>
                    <label for="alterNome">Nome do Pet: </label>
                    <input type="text" name="alterNome" id="alterNome" value="<?= htmlspecialchars($pet['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label for="alterPetNasc">Data de nascimento: </label>
                    <input type="date" name="alterPetNasc" id="alterPetNasc" value="<?= htmlspecialchars($pet['dataNascimento'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="radio">
                    <label>Porte: </label>
                    <label for="pequeno">Pequeno</label>
                    <input                     type="radio" name="porte" id="pequeno" value="pequeno" <?= $pet['porte'] == 'pequeno' ? 'checked' : '' ?>>
                    <label for="medio">Médio</label>
                    <input type="radio" name="porte" id="medio" value="medio" <?= $pet['porte'] == 'medio' ? 'checked' : '' ?>>
                    <label for="grande">Grande</label>
                    <input type="radio" name="porte" id="grande" value="grande" <?= $pet['porte'] == 'grande' ? 'checked' : '' ?>>
                </div>
                <div class="raca">
                    <label>Raça: </label>
                    <select name="raca" id="raca">
                        <option value="<?= htmlspecialchars($pet['raca'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($pet['raca'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <option value="null">Selecione uma opção</option>
                        <option value="labrador">labrador</option>
                        <option value="golden retriever">golden retriever</option>
                        <option value="dalmata">dalmata</option>
                        <option value="bulldog">bulldog</option>
                        <option value="pitbull">pitbull</option>
                        <option value="pincher">pincher</option>
                    </select>
                </div>
                <div class="radio">
                    <label>Sexo: </label>
                    <label for="macho">Macho</label>
                    <input type="radio" name="sexo" id="macho" value="M" <?= $pet['sexo'] == 'M' ? 'checked' : '' ?>>
                    <label for="femea">Fêmea</label>
                    <input type="radio" name="sexo" id="femea" value="F" <?= $pet['sexo'] == 'F' ? 'checked' : '' ?>>
                </div>
                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" cols="30" rows="4" placeholder="Fale um pouco sobre o pet"><?= htmlspecialchars($pet['descricao'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="dropzone-box">
                    <label>Adicione as fotos: </label>
                    <div class="dropzone-area">
                        <div class="uploadIcon">ICONE</div>
                        <input type="file" id="uploadFoto" name="uploadFoto">
                        <p class="fotoInfo">Sem arquivo selecionado</p>
                    </div>
                    <div class="dropzone-actions">
                        <button type="reset" onclick="window.location.href='pets.php'">Cancelar</button>
                    </div>
                </div>
                <div id="submit">
                    <input type="submit" value="Salvar">
                </div>
            </form>
        </div>
    </main>
    <script src="../../js/script.js"></script>
    <script src="../../js/alert.js"></script>
</body>
</html>

