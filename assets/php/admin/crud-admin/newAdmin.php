<?php
session_start();

require __DIR__ . '/../../conexao.php';
# solicita a conexão com o banco de dados e guarda na váriavel dbh.
$dbh = Conexao::getConexao();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área Restrita</title>
  <link rel="stylesheet" href="../../../css/dashboards.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <style>
    input,
    label {
      height: 25px !important;
      margin: 5px 0px 5px 2px;
    }

    input {
      width: 200px !important;
      height: 25px !important;
      margin-right: 10px !important;
      border: 1px solid black !important;
      border-radius: 10px !important;
    }

    input#alterDataNasc {
      width: 105px !important;
    }

    input#alterSenha {
      width: 161px !important;
    }
    #erro{display: none;}
  </style>
</head>

<body>
  <header>
    <button class="nav-toggle"><span class="material-symbols-outlined">
        menu
      </span></button>
    <figure class="logo"><img src="../../../img/logo1.png" alt=""></figure>
    <div class="user-info">Bem-vindo,
      <?= $_SESSION['nome']; ?> <span id="username"></span>
    </div>
  </header>
  <nav>
    <ul>
      <li><a href="../crud-pet/pets.php">Pets cadastrados</a></li>
      <li><a href="../crud-usuario/listaUsuarios.php">Usuários cadastrados</a></li>
      <li><a href="../administrador_dashboard.php">Meu Perfil</a></li>
      <li><a href="../listaAdmin.php">Administradores</a></li>
      <li><a href="../../logout.php">Sair</a></li>
    </ul>
  </nav>
  <div class="content" id="conteudo">
    <h1>Cadastrar Administrador</h1>
    <form action="newAdmin.php" method="post" enctype="multipart/form-data">
      <div>
        <label for="admiNome">Nome: </label>
        <input type="text" name="adminNome" id="admiNome">
        <br>
        <label for="admiNome">Sobrenome: </label>
        <input type="text" name="adminSobrenome" id="admiSobrenome">
        <br>
        <label for="admiNome">Cargo: </label>
        <input type="text" name="adminCargo" id="adminCargo">
        <br>
        <label for="admiNome">Email: </label>
        <input type="email" name="adminEmail" id="adminEmail">
        <br>
        <label for="admiNome">Senha: </label>
        <input type="password" name="adminSenha" id="adminSenha">
        <br><br>
        <button type="submit">Cadastrar</button>
      </div>

      <script src="../../../js/script.js"></script>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  function validarEntrada($data)
  {
    return htmlspecialchars(stripslashes(trim($data)));
  }

  $adminNome = validarEntrada($_POST['adminNome']);
  $adminSobrenome = validarEntrada($_POST['adminSobrenome']);
  $adminEmail = filter_var(validarEntrada($_POST['adminEmail']), FILTER_SANITIZE_EMAIL);
  $adminCargo = validarEntrada($_POST['adminCargo']);
  $adminSenha = password_hash(validarEntrada($_POST['adminSenha']), PASSWORD_DEFAULT);

  if (empty($adminNome) || empty($adminSobrenome) || empty($adminEmail) || empty($adminSenha)) {
    echo '<p>Por favor, preencha todos os campos obrigatórios.</p>';
    echo "<p><a href='index.php'>Voltar</a></p>";
    exit;
  }

  # Verifica se o e-mail é válido
  if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
    echo "<p><script>window.alert('E-mail inválido')</script></p>";
    exit;  // Adicione um exit para interromper a execução do script em caso de e-mail inválido
  }

  try {
    $dbh = Conexao::getConexao();

    $query = "INSERT INTO caopanheiro.administrador (nome, sobrenome, email, senha) VALUES (:nome, :sobrenome, :email, :senha)";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':nome', $adminNome);
    $stmt->bindParam(':sobrenome', $adminSobrenome);
    $stmt->bindParam(':email', $adminEmail);
    $stmt->bindParam(':senha', $adminSenha);

    $result = $stmt->execute();
    if ($result) {
      echo "<script>alert('Cadastrado com Sucesso!')</script>";
      header('Location: ../listaAdmin.php?status=success');
      exit;
    } else {
      echo '<p>Não foi possível inserir Usuário!</p>';
      $error = $stmt->errorInfo();
      print_r($error);
    }
  } catch (PDOException $e) {
    echo '<p>Erro ao conectar-se ao banco de dados: ' . $e->getMessage() . '</p>';
  } finally {
    # Fecha a conexão
    $dbh = null;
  }
  echo "<p><a href='newAdmin.php'>Voltar</a></p>";
} else {
  echo '<p id="erro">Método de requisição inválido.</p>';
}
?>