<?php
    session_start();
    require_once("../../conexao.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Home</title>

    <!--Local-->
    <link rel="stylesheet" href="../assets/css/home.css">
    <script src="../assets/js/new home.js" defer></script>

    <!--header.js-->
    <link rel="stylesheet" href="../assets/css/header.css">
    <script src="../assets/js/header.js" defer></script>

    
</head>
<body>

    <!--Importar 'header.js' em assets/js-->
    <!--Iportar 'header.css' em assets/css-->
    <?php include('../includes/header.php');?>
    
    <div class="container">
        <section id="destaques" class="texto theme-background"></section>

        <h1 id="titulos">Feed</h1>

        <section id="feed">
            <!--Consulta SQL com mysqli O.O.-->

            
        </section>

        <button id="mais-com" class="texto theme-background">Ver mais</button>
        <div>
            <img src="../assets/images/sppiner-transparent.gif" alt="Carregando..." id="sppiner">
        </div>

    </div>

    <footer>
        &copy; emanoeldev | Direitos Reservados
    </footer>

    <!--Jquery-->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</body>
</html>