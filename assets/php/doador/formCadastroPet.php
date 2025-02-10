<?php
session_start();
require __DIR__ . '/../conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Restrita</title>
    <link rel="stylesheet" href="../../css/dashboards.css">
    <link rel="stylesheet" href="../../css/cadastroPet.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body{height: 117vh;}
        nav {height: 118vh;}
        .content {height: 100%;}
    </style>
</head>

<body>
    <header>
        <button class="nav-toggle"><span class="material-symbols-outlined">
                menu
            </span></button>
        <figure class="logo"><img src="../../img/logo1.png" alt="Logo"></figure>
        <div class="user-info">Bem-vindo, <?= htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?> <span id="username"></span></div>
    </header>
    <nav>
        <ul>
            <li><a href="pets.php">Meus Pets</a></li>
            <li><a href="doador_dashboard.php">Meu Perfil</a></li>
            <li><a href="chatsDoador.php">Conversas</a></li>
            <li><a href="../logout.php">Sair</a></li>
        </ul>
    </nav>
    <div class="content" id="conteudo">
        <h1>Cadastrar pets</h1>
        <?php
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="alert">
                <span class="closebtn" onclick="this.parentElement.style.display = \'none\';">&times;</span>
                Pet cadastrado com sucesso!
                </div>';
        }
        ?>
        <form action="cadastrarPets.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="petNome">Nome do Pet: </label>
                <input type="text" name="petNome" id="petNome" required>
            </div>
            <div>
                <label for="petNasc">Data de nascimento: </label>
                <input type="date" name="petNasc" id="petNasc" required>
            </div>

            <div class="radio">
                <label>Espécie: </label>
                <label for="cachorro">Cachorro</label>
                <input type="radio" name="especie" id="cachorro" value="cachorro" required>
                <label for="gato">Gato</label>
                <input type="radio" name="especie" id="gato" value="gato" required>
            </div>
            <div class="raca" id="raca-container" style="display: none;">
                <label>Raça: </label>
                <select name="raca" id="raca" required>
                    <option value="null">Selecione uma opção</option>
                </select>
            </div>

            <div class="radio">
                <label>Porte: </label>
                <label for="pequeno">Pequeno</label>
                <input type="radio" name="porte" id="pequeno" value="pequeno" required>
                <label for="medio">Médio</label>
                <input type="radio" name="porte" id="medio" value="medio" required>
                <label for="grande">Grande</label>
                <input type="radio" name="porte" id="grande" value="grande" required>
            </div>

            <div class="radio">
                <label>Sexo: </label>
                <label for="macho">Macho</label>
                <input type="radio" name="sexo" id="macho" value="M" required>
                <label for="femea">Fêmea</label>
                <input type="radio" name="sexo" id="femea" value="F" required>
            </div>
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" cols="30" rows="4" placeholder="Fale um pouco sobre o pet" required></textarea>

            <div class="dropzone-box">
                <label>Adicione as fotos: </label>
                <div class="dropzone-area">
                    <div class="uploadIcon"><svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#5f6368">
                            <path d="M450-313v-371L330-564l-43-43 193-193 193 193-43 43-120-120v371h-60ZM220-160q-24 0-42-18t-18-42v-143h60v143h520v-143h60v143q0 24-18 42t-42 18H220Z" />
                        </svg></div>
                    <input type="file" id="uploadFoto" name="uploadFoto" accept="image/*" required>
                </div>
                <div class="dropzone-actions">
                    <button type="reset">Cancelar</button>
                </div>
            </div>
            <div id="submit">
                <input type="submit" value="Salvar">
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const especieRadios = document.querySelectorAll('input[name="especie"]');
            const racaContainer = document.getElementById('raca-container');
            const racaSelect = document.getElementById('raca');

            const racas = {
                cachorro: [
                    'labrador', 'golden retriever', 'dalmata', 'bulldog', 'pitbull', 'pincher',
                    'beagle', 'boxer', 'chihuahua', 'cocker spaniel', 'dachshund', 'doberman',
                    'german shepherd', 'poodle', 'rottweiler', 'schnauzer', 'shih tzu', 'yorkshire terrier'
                ],
                gato: [
                    'persa', 'siamês', 'maine coon', 'bengal', 'sphynx',
                    'angorá', 'ragdoll', 'savannah', 'abissínio', 'birmanês',
                    'chartreux', 'himalayan', 'manx', 'norwegian forest', 'scottish fold'
                ]
            };

            especieRadios.forEach(radio => {
                radio.addEventListener('change', (event) => {
                    const especie = event.target.value;
                    racaSelect.innerHTML = '<option value="null">Selecione uma opção</option>';
                    if (racas[especie]) {
                        racas[especie].forEach(raca => {
                            const option = document.createElement('option');
                            option.value = raca;
                            option.textContent = raca;
                            racaSelect.appendChild(option);
                        });
                        racaContainer.style.display = 'block';
                    } else {
                        racaContainer.style.display = 'none';
                    }
                });
            });
        });
    </script>
    <script src="../../js/alert.js"></script>
</body>

</html>