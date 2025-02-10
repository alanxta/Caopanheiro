<?php
require __DIR__ . '/../conexao.php';

session_start();

function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags($data));
}


    if ($_SESSION['perfil'] === 'adotante') {
        $doadorId = isset($_GET['destinatario']) ? sanitizeInput($_GET['destinatario']) : null;
        $adotanteId = $_SESSION['usuId'];

        if (!empty($doadorId) && !empty($adotanteId) && is_numeric($doadorId) && is_numeric($adotanteId)) {
            try {
                $dbh = Conexao::getConexao();
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Verifica se já existe um chat entre o doador e o adotante
                $stmt_check = $dbh->prepare("SELECT COUNT(*) FROM chats WHERE doador = :doador AND adotante = :adotante");
                $stmt_check->bindParam(':doador', $doadorId, PDO::PARAM_INT);
                $stmt_check->bindParam(':adotante', $adotanteId, PDO::PARAM_INT);
                $stmt_check->execute();
                $chat_exists = $stmt_check->fetchColumn();

                if (!$chat_exists) {
                    // Se o chat ainda não existir, insere um novo
                    $stmt_insert = $dbh->prepare("INSERT INTO chats (doador, adotante) SELECT :doador, :adotante WHERE NOT EXISTS (SELECT 1 FROM chats WHERE doador = :doador AND adotante = :adotante)");
                    $stmt_insert->bindParam(':doador', $doadorId, PDO::PARAM_INT);
                    $stmt_insert->bindParam(':adotante', $adotanteId, PDO::PARAM_INT);
                    $result = $stmt_insert->execute();

                    if ($result) {
                        echo "<script>alert('Chat Iniciado')</script>";
                        echo "<script>window.location.href='listaChats.php'</script>";
                        exit();
                    } else {
                        echo 'ERRO!';
                    }
                } else {
                    echo "<script>alert('O chat já existe')</script>";
                    echo "<script>window.location.href='listaChats.php'</script>";
                    exit();
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('IDs não identificados')</script>";
            exit();
        }
    } else {
        echo 'ERRO';
    }

?>
