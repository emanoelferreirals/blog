<?php
    session_start();

    if($_SESSION['login-admin']){

        require_once("../../conexao.php"); ?>
        
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicação</title>
    <link rel="stylesheet" href="css/publicacao.css">
</head>
<body>     

    <?php
        if(isset($_GET['id']) &&
        !empty($_GET['id'])) {

            global $conn;
            $id = $_GET['id'];
            
            $query = "SELECT * FROM publicacoes WHERE id_publi=$id;";

            $result = mysqli_query($conn,$query);

            while($dados = mysqli_fetch_assoc($result)){
        ?>

        <h1><?=$dados['titulo']?></h1>

        <div class="conteudo"><?=$dados['conteudo']?></div>

        <div class="botoes">
            <a href="editor-admin.php?id=<?=$id?>">Editar</a>

            <a href="" id="butao-excluir">Excluir</a>
        </div>

        <form action="acoes-admin.php" method="post">
            <input type="hidden" name="operacao" value="deletar">

            <input type="hidden" name="id_del" value="<?=$id?>">

            <button type="submit">Excluir</button>
        </form>

        <?php
            }
        } ?>

<div class="list-comentarios" style="border: 1px solid white;">
        <h2>Comentários</h2>

        <?php

            $query_com = "SELECT * FROM comentarios WHERE id_publi_com=$id";

            $result_comentarios = mysqli_query($conn,$query_com);
            
            $foto_perfil = '../imgs/foto-perfil-padrao.png';

            while($comentarios = mysqli_fetch_assoc($result_comentarios)){
                $id_user_com = $comentarios['id_usuario_com'];
                $user_com = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM usuario WHERE id_usuario=$id_user_com"));

                $directory = '../users/user' . $user_com['id_usuario'] . '/';

                if (is_dir($directory)) {
                    $png_file = $directory . 'foto-perfil.png';
                    $jpg_file = $directory . 'foto-perfil.jpg';

                    if (file_exists($png_file)) {
                        $foto_perfil = $png_file;
                    } elseif (file_exists($jpg_file)) {
                        $foto_perfil = $jpg_file;
                    }
                }?>
             
            
                <div class="usuario-com">
                    <div class="foto-perfil" style="background-image: url(<?=$foto_perfil?>)"></div>

                    <p class="id-usuario-com"> 
                        <?=$user_com['email'];?>
                    </p>
                </div>
                    
                 <p class="id-publi-com" style="display: none;">
                     <?=$comentarios['id_publi_com'];?>
                 </p>
                 <p class="comentario">
                     <?=$comentarios['texto'];?>
                 </p>
            <?php } ?>
        </div>     
   
<?php
    }
?>

</body>
</html> 