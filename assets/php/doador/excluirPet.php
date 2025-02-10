<?php
    session_start();
    require __DIR__ . '/../conexao.php';
    $dbh = Conexao::getConexao();

    $petId= $_GET['Id'];
    # cria o comando DELETE filtrado pelo campo id
    $query = "DELETE FROM pets WHERE petId = :petId;";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':petId', $petId);
    $stmt->execute();

    if ($stmt->rowCount() == 1)
    {
        echo "<script>window.alert('Pet excluido com sucesso')</script>";
        echo "<script>window.location.href = 'pets.php'</script>";
        exit;
    } else {
        echo "<script>window.alert('Erro ao excluir o Pet')</script>";
        echo "<script>window.location.href = 'pets.php'</script>";
    }
    $dbh = null;
    