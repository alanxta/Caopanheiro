<?php
session_start();
require __DIR__ . '/assets/php/conexao.php';

$dbh = Conexao::getConexao();

$query = "SELECT * FROM caopanheiro.pets where status = 'disponivel' Limit 6";
$stmt = $dbh->prepare($query);
$stmt->execute();
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cãopanheiro</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mediaquery.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="assets/img/logo1.png" alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Sobre nós</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="assets/php/catalogo.php">Pets Disponíveis</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="assets/php/Paglogin.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <div class="home">
            <section class="home">
                <h1>Cãopanheiro</h1>
                <h2>Adote o seu amigo</h2>
                <p>Bem-vindo ao nosso site de adoção de pets, Cãopanheiro. Navegue pelo nosso catálogo de pets disponíveis para adoção e descubra como você pode transformar a vida de um amigo de quatro patas</p>
                <button class="home" href="assets/php/Paglogin.php">Quero adotar</button>
            </section>
            <figure class="cachorro"><img src="assets/img/dogPrincip.webp" alt="" id="cachorro"></figure>
        </div>

        <section class="catalogo">
            <h1>Pets Disponíveis</h1>
            <div id="petCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $chunks = array_chunk($pets, 3);
                    foreach ($chunks as $index => $chunk) :
                    ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                            <div class="row">
                                <?php foreach ($chunk as $pet) : ?>
                                    <div class="col-md-4">
                                        <div class="pet-card">
                                            <img src="assets/php/<?= htmlspecialchars($pet['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto do Pet">
                                            <div class="pet-info">
                                                <h2><?= htmlspecialchars($pet['nome'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                                <p><strong>Raça:</strong> <?= htmlspecialchars($pet['raca'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                <p><strong>Sexo:</strong> <?= htmlspecialchars($pet['sexo'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                <p><strong><?= htmlspecialchars($pet['status'] == 'adotado' ? 'Adotado' : 'Disponível', ENT_QUOTES, 'UTF-8'); ?></strong> </p>
                                                <p style="display:none;"><strong>Doador:</strong> <?= htmlspecialchars($pet['doador'], ENT_QUOTES, 'UTF-8'); ?></p>
                                               
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#petCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#petCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <a href="assets/php/catalogo.php" class="link" id="catalogo">Encontre seu Cãopanheiro</a>
            </div>
        </section>
        <section class="sobre_nos">
            <h1>Sobre nos</h1>
            <p>Somos o Cãopanheiroum lugar dedicado a conectar animais de estimação carentes com lares amorosos e responsáveis. Nossa missão é promover a adoção consciente, oferecendo informações detalhadas sobre cada pet, incluindo sua história, personalidade e necessidades especiais. Acreditamos que todo animal merece uma segunda chance e um lar onde possa ser amado e cuidado. Juntos, podemos fazer a diferença e proporcionar um futuro melhor para esses animais.</p>
            <figure><img src="assets/img/gato.png" alt=""></figure>
            
        </section>

        <h1 id="card">Por que adotar?</h1>
        <section class="container_cards">
            <div class="cards">
                <div class="img"><img src="assets/img/card1.png" alt=""></div>
                <div class="conteudo">
                    <p>Nesse exato momento,existem milhares de doguinhos e gatinhos esperando um humano para chamar de seu.</p>
                </div>
            </div>
            <div class="cards">
                <div class="img"><img src="assets/img/card2.png" alt=""></div>
                <div class="conteudo">
                    <p>E não há recompensa maior do que vê-los se tornando aquela coisinha alegre e saudável depois de uma boa dose de cuidado e carinho.</p>
                </div>
            </div>
            <div class="cards">
                <div class="img"><img src="assets/img/card3.png" alt=""></div>
                <div class="conteudo">
                    <p>Pensando bem, a pergunta é outra: se você pode mudar o destino de um animal de rua, por que não faria isso?</p>
                </div>
            </div>
        </section>

        
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">

   

    </script>
</body>

</html>
