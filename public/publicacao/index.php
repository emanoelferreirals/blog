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

    <!--Styles-->
    <link rel="stylesheet" href="../assets/css/publicacao.css">
    <link rel="stylesheet" href="../assets/css/comentarios.css">
    <link rel="stylesheet" href="../assets/css/header.css">

    <!--Scripts-->
    <script src="../assets/js/publicacao.js" defer></script>
    <script src="../assets/js/comentarios.js" defer></script>
    <script src="../assets/js/header.js" defer></script>
    
</head>
<body>
    <?php include('../includes/header.php');?>

    <?php
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id_publi = $_GET['id'];

        // Certifique-se de usar a conexão correta
        $queryConsult = "SELECT * FROM publicacoes WHERE id_publi='$id_publi'";
        $result = mysqli_query($conn, $queryConsult);

        if ($result) {
            // Verifique se existem resultados
            if (mysqli_num_rows($result) > 0) {
                $publicacao = mysqli_fetch_assoc($result);
                if ($publicacao) {
                    // Consulta para contar os comentários
                    $queryCount = "SELECT COUNT(*) as num_comentarios FROM comentarios WHERE id_publi_com='$id_publi'";
                    $countResult = mysqli_query($conn, $queryCount);

                    if ($countResult) {
                        $countRow = mysqli_fetch_assoc($countResult);
                        $num_comentarios = $countRow['num_comentarios'];
                    } else {
                        $num_comentarios = 0;
                    }
                    ?>
                    <div class="container">
                        <div class="publi texto theme-background">
                            <p class="data"><?= date("d/m/Y", strtotime($publicacao['data_publicacao'])); ?> às 
                                <?= date("H:i", strtotime($publicacao['data_publicacao'])); ?>
                            </p> 

                            <div>
                                <h1 class="titulo">
                                    <?= htmlspecialchars($publicacao['titulo']); ?>
                                </h1>
                                <p class="subtitulo">
                                    <?= htmlspecialchars($publicacao['descricao']); ?>
                                </p>
                                <div class="conteudo">
                                    <?= $publicacao['conteudo']; ?>
                                </div>
                            </div>

                            <div class="controls-admin">
                                <!-- Adicione controles de administração aqui se necessário -->
                            </div>

                            <div class="insights">
                                <div id="like">
                                    <?php
                                    global $conn;

                                    $query_num_curtidas = "SELECT * FROM curtidas WHERE id_publi_curt=$id_publi";
                                    $result_num_curtidas = mysqli_query($conn, $query_num_curtidas);
                                    $num_curtidas = mysqli_num_rows($result_num_curtidas);

                                    if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id']) {
                                        $id_user = $_SESSION['usuario_id'];

                                        $query_consult = "SELECT * FROM curtidas WHERE id_user_curt=$id_user AND id_publi_curt=$id_publi";
                                        $result_consult = mysqli_query($conn, $query_consult);
                                        $curtida_satus = mysqli_num_rows($result_consult); 
                                    
                                        echo $curtida_satus > 0 ? "<p>❤️$num_curtidas</p>" : "<p>🤍$num_curtidas</p>";
                                    } else {
                                        echo "<p>🤍$num_curtidas</p>";
                                    }
                                    ?>                        
                                </div>
                                <div id="open-comentarios">
                                    <p>🗨️<span id="num-com"><?= $num_comentarios ?></span></p>
                                </div>
                                <div class="problema">
                                    <a href="">⚠️<br> Informar problema</a>
                                </div>
                            </div>
                        </div>      

                        <!--Area comentarios-->
                        <div class="comentarios-area texto theme-background">
                            <h1>Comentários</h1>
                            <form id="form-coment">
                                <input type="hidden" name="operacao" value="comentario">
                                <input type="hidden" name="id_publi_com" value="<?= $publicacao['id_publi']; ?>">
                                <input type="text" name="comentario" id="comentario" placeholder="Fazer comentário..." minlength="1">

                                <button type="submit" class="btn-comentar" id="btn-comentar" class="button">Comentar</button>
                            </form>

                            <div class="list-comentario texto theme-background" id="list-comentario">
                                <?php // include('../includes/comentarios.php'); ?>
                            </div>
                            <div id="sppiner">
                                <img src="../assets/images/sppiner-transparent.gif" alt="Carregando...">
                            </div>
                            <button id="mais-com" class="texto">Ver mais</button>
                        </div>  

                        <div class="background-tela-confirme">
                            <div class="area-confirme-excluir">
                                <p>Tem certeza que quer excluir seu comentário?</p>
                                <div>
                                    <button id="btn-cancelar">Cancelar</button>
                                    <button id="btn-confirme-excluir">Excluir</button>
                                </div>
                            </div>
                        </div>
                    </div>      

                    <footer>
                        &copy; Direitos Reservados
                    </footer> 
                    <?php
                } // Fechando if ($publicacao)
            } else {
                ?>
                <div class='container'>
                    <h1 class='titulo'>Publicação não encontrada!</h1>
                </div>
                <footer class='bottom'>
                    &copy; Direitos Reservados
                </footer> 
                <?php
            }
        } else {
            ?>
            <h1>Erro ao executar a consulta!</h1>
            <?php
        }
    } else {
        ?>
        <h1>ID da publicação não fornecido!</h1>
        <?php
    }
    ?>
        
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <script>
        <?php 
        $id_user = false;

        if (isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id'])) {
            $id_user = $_SESSION['usuario_id'];
            echo "var idUser = $id_user;";
        } else {
            echo "var idUser = false;";
        }
        ?>

        var idPubli = <?= isset($_GET['id']) ? $_GET['id'] : 'null'; ?>;
    </script>
</body>
</html>