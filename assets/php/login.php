<?php
session_start();
// Conexão com o banco de dados MySQL usando PDO
require_once 'conexao.php';

// Verifica se os campos foram enviados
if (isset($_POST['emailLogin']) && isset($_POST['senhaLogin'])) {
    $emailLogin = filter_var($_POST['emailLogin'], FILTER_SANITIZE_EMAIL);
    $senhaLogin = $_POST['senhaLogin'];  // Senha em texto plano

    // Solicita a conexão com o banco de dados e guarda na variável dbh.
    $dbh = Conexao::getConexao();

    // Verifica se a conexão foi estabelecida
    if ($dbh) {
        // Consulta preparada para verificar se o usuário existe no banco de dados e está ativo
        $query = "
        SELECT 
            u.usuarioId, u.nome, u.sobrenome, u.data_nascimento, u.cpf, u.endereco, u.email, u.senha, u.perfil, u.status 
        FROM 
            usuario u
        WHERE 
            u.email = :email AND u.status = 'ativo'
        UNION
        SELECT 
            a.adminId AS usuarioId, a.nome AS nome, a.sobrenome AS sobrenome, NULL AS data_nascimento, NULL AS cpf, NULL AS endereco, a.email AS email, a.senha AS senha, a.perfil AS perfil, a.status 
        FROM 
            administrador a
        WHERE 
            a.email = :email AND a.status = 'ativo'
        ";

        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $emailLogin);
        $stmt->execute();

        // Verifica se a consulta retornou algum resultado
        if ($stmt->rowCount() > 0) {
            // Recupera as informações do usuário
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica se o campo Senha está presente e não é nulo
            if (isset($user['senha']) && !is_null($user['senha'])) {
                // Verifica se a senha fornecida corresponde ao hash armazenado
                if (password_verify($senhaLogin, $user['senha'])) {
                    $isAdmin = isset($user['perfil']) && $user['perfil'] === 'administrador';

                    $_SESSION['perfil'] = $isAdmin ? 'administrador' : (isset($user['perfil']) ? $user['perfil'] : 'adotante');
                    $_SESSION['nome'] = $user['nome'];
                    $_SESSION['sobrenome'] = $user['sobrenome'];
                    $_SESSION['data'] = $user['data_nascimento'] ? date('Y-m-d', strtotime($user['data_nascimento'])) : NULL;
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['cpf'] = $user['cpf'];
                    $_SESSION['endereco'] = $user['endereco'];
                    $_SESSION['usuId'] = $user['usuarioId'];                   
                    $_SESSION['status'] = isset($user['status']) ? $user['status'] : 'ativo';
                    
                    // Redireciona para a página correspondente ao perfil do usuário
                    switch ($_SESSION['perfil']) {
                        case 'adotante':
                            header("Location: adotante/adotante_dashboard.php");
                            exit();
                        case 'doador':                          
                            header("Location: doador/doador_dashboard.php");
                            exit();
                        case 'administrador':   
                            header("Location: admin/administrador_dashboard.php");
                            exit();
                        default:
                            echo "Perfil inválido!";
                            exit();
                    }
                } else {
                    // Senha incorreta
                    echo "<script>window.alert('Usuário ou senha incorretos!')</script>";
                    echo "<script>window.location.href = 'Paglogin.php'</script>";
                    exit();
                }
            } else {
                // Senha não encontrada no resultado da consulta
                echo "<script>window.alert('Erro no sistema: senha não encontrada.')</script>";
                echo "<script>window.location.href = 'Paglogin.php'</script>";
                exit();
            }
        } else {
            // E-mail não encontrado ou usuário inativo
            echo "<script>window.alert('Usuário ou senha incorretos!')</script>";
            echo "<script>window.location.href = 'Paglogin.php'</script>";
            exit();
        }
    } else {
        // Falha na conexão com o banco de dados
        echo "<script>window.alert('Erro ao conectar-se ao banco de dados!')</script>";
        echo "<script>window.location.href = 'Paglogin.php'</script>";
        exit();
    }
} else {
    // Campos de login não foram enviados
    echo "<script>window.alert('Por favor, preencha os campos de login!')</script>";
    echo "<script>window.location.href = 'Paglogin.php'</script>";
    exit();
}
