<?php 
    require_once('../../conexao.php');
    session_start();

    $_SESSION['login-admin'] = false;
    
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login admin</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="area-login">
        <form action="index.php" method="post" id="login">
            <label for="user">Email admin</label><br>
            <input type="email" name="email-admin" id="email-login"><br>

            <label for="senha-login">Senha admin:</label><br>
            <input type="password" name="senha-admin" id="senha-login"><br>

            <button type="submit">Entrar</button>
        </form>

    <?php
        if(isset($_POST['email-admin']) && !empty($_POST['email-admin']) &&
        isset($_POST['senha-admin']) && !empty($_POST['senha-admin'])){
            $email = $_POST['email-admin'];
            $senha = $_POST['senha-admin'];
    
            global $conn;
    
            $query = "SELECT * FROM usuario_admin WHERE email='$email' AND senha='$senha';";
    
            $result = mysqli_query($conn, $query);
            
            if($result && mysqli_num_rows($result) > 0){
                $_SESSION['login-admin'] = true;
    
                header('Location: editor-admin.php');
                exit;
            }else{
                echo "<p class='error'>email e/ou senha errados</p>";
            }
        }
    ?>
    </div>
</body>
</html>