<?php
session_start();
require __DIR__ . '/../conexao.php';

$dbh = Conexao::getConexao();

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idChat = sanitizeInput($_POST['idChat']);

    if (!empty($idChat) && is_numeric($idChat)) {
        try {
            $stmt = $dbh->prepare("DELETE FROM chat WHERE idChat = :idChat");
            $stmt->bindParam(':idChat', $idChat, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "Chat deleted successfully";
            } else {
                echo "Error deleting chat.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid input.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Chat</title>
</head>
<body>
    <form method="post" action="">
        <label for="idChat">Chat ID:</label>
        <input type="text" id="idChat" name="idChat" required><br>
        <input type="submit" value="Delete Chat">
    </form>
</body>
</html>
