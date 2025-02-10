<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>

    <section>
        <header>

            <nav>
                <ul>
                    <li><a href="../../index.php">Home</a></li>
                </ul>

            </nav>
        </header>
        <div class="formbox" id="box">

            <div class="imagem" id="imagem">

            </div>
            <div class="login" id="login">
                <form action="login.php" autocomplete="on" method="post">
                    <h2>Login</h2>

                    <div class="inputbox">
                        <input type="email" name="emailLogin" id="emailLogin" required autocomplete="email">
                        <label for="emailLogin">E-mail</label>
                    </div>
                    <div class="inputbox">
                        <input type="password" name="senhaLogin" id="senhaLogin" required autocomplete="current-password" minlength="5" maxlength="20">
                        <label for="senhaLogin">Senha</label>
                    </div>
                    <button class="env">Entrar</button>

                </form>
                <button onclick="trocaLado()" class="regs">Registrar-se</button>
                <br><br>

                <button class="regs"><a href="formRecuperar.php">Esqueci minha senha</a></button>
            </div>
            <div class="registro" id="registro">
                <form action="cadastro.php" method="post" autocomplete="on">
                    <h2>Registrar</h2>
                    <div class="inputbox">
                        <input type="text" name="nome" id="nome" required>
                        <label for="nome">Nome</label>
                    </div>
                    <div class="inputbox">
                        <input type="email" name="email" id="email" required>
                        <label for="email">E-mail</label>
                    </div>
                    <div class="inputbox">
                        <input type="password" name="senha" id="senha" required>
                        <label for="senha">Senha</label>
                    </div>
                    <button class="env">Continuar</button>
                </form>
                <button class="regs" onclick="trocaLado()">Sou cadastrado</button>

            </div>

        </div>
    </section>
    <script>
        function trocaLado() {
            let imagem = document.getElementById("imagem");

            if (imagem.style.transform == "translateX(0%)") {
                imagem.style.transform = "translateX(100%)";
            } else {
                imagem.style.transform = "translateX(0%)";
            }
        }
    </script>
</body>

</html>