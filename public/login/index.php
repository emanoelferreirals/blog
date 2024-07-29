<?php
    session_start();
     $_SESSION['login'] = false;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Cadastre-se</title>
    <link rel="stylesheet" href="../assets/css/login-cadastro.css">
</head>
<body>
    <div class="escolha">
        <p class="btn-login">Login</p>
        <p class="btn-cadastro">Cadastrar-se</p>
    </div>

    <div class="area-login">
        <form action="../acoes.php" method="post" id="login">
            <?php 
                if(isset($_GET['redirect']) && !empty($_GET['redirect'])){
                    $redirect = $_GET['redirect'];

                    echo '<input type="hidden" name="redirect" value="'.$redirect.'">';
                }
            ?>
            <input type="hidden" name="operacao" value="login">

            <label for="user">Email de usuário:</label><br>
            <input type="text" name="email-login" id="email-login"><br>

            <label for="senha-login">Senha:</label><br>
            <input type="password" name="senha-login" id="senha-login"><br>

            <button type="submit" class="btn-submit">Entrar</button>
        </form>
    </div>

    <div class="area-cadastro">
        <form action="../acoes.php" method="post" id="cadastro">
            <input type="hidden" name="operacao" value="cadastro">

            <label for="user">Usuário:</label><br>
            <input type="text" name="user-cadastro" id="user-cadastro"><br>

            <label for="email">Email:</label><br>
            <input type="email" name="email-cadastro" id="email-cadastro"><br>

            <label for="senha">Senha:</label><br>
            <input type="password" name="senha-cadastro" id="senha-cadastro"><br>

            <label for="senha">Confirme Senha:</label><br>
            <input type="password" name="confirme-senha" id="confirme-senha"><br>

            <button type="submit" class="btn-submit">Cadastrar-se</button>
        </form>
    </div>

    <script>
        const btnLogin = document.querySelector('.btn-login');
        const btnCadastro = document.querySelector('.btn-cadastro');
        const viewLogin = document.querySelector('.area-login');
        const viewCadastro = document.querySelector('.area-cadastro');

        btnCadastro.addEventListener('click',()=>{
            btnCadastro.style.backgroundColor = '#d4d4d4c9';
            btnLogin.style.backgroundColor = 'transparent';
            viewLogin.style.display = 'none';
            viewCadastro.style.display = 'block';
        });
        

        btnLogin.addEventListener('click',()=>{
            btnLogin.style.backgroundColor = '#d4d4d4c9';
            btnCadastro.style.backgroundColor = 'transparent';
            viewCadastro.style.display = 'none';
            viewLogin.style.display = 'block';
        })

    </script>
</body>
</html>