<?php 
    require_once("../../conexao.php");
    session_start();

    if($_SESSION['login-admin']){
        //echo "mostar";
        
 ?>
 <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/acess-admin.css">
    <!--trumbowyg style-->
    <link rel="stylesheet" href="dist/ui/trumbowyg.min.css">
</head>
<body>
    <div class="container">

        <form action="../acoes.php" method="post">
            <input type="hidden" name="operacao" value="deslogar-adm">
            <button type="submit" class="buttom"><= Sair da conta</button>
        </form>

<?php
    if(isset($_GET['id']) &&
    !empty($_GET['id'])) {
        
        global $conn;
        $id = $_GET['id'];
        
        $query = "SELECT * FROM publicacoes WHERE id_publi=$id;";

        $result = mysqli_query($conn,$query);
?>

        <?php while($dados = mysqli_fetch_assoc($result)){ ?>

        <form action="acoes-admin.php" method="post">
            <input type="hidden" name="operacao" value="editar">

            <input type="hidden" name="id_edit" value="<?=$id?>">

            <label for="titulo-edit">Titulo:</label>
            <input type="text" name="titulo-edit" id="titulo" value="<?=$dados['titulo']?>"><br>

            <label for="descricao">Descrição:</label>
            <input type="text" name="descricao" id="descricao"><br>

            <label for="conteudo-edit">Conteudo:</label><br>
            <textarea name="conteudo-edit" id="conteudo" cols="100" rows="20" onkeypress="verificarEnter(event)">
                <?=$dados['conteudo']?>
            </textarea><br>
            
            <button type="submit">Editar</button>
        </form><br>
            
        <div id="previl"></div>
    

<?php 
        }
    }else{
?>
        <form action="acoes-admin.php" method="post">
            <input type="hidden" name="operacao" value="publicar">
            
            <label for="titulo">Titulo:</label>
            <input type="text" name="titulo" id="titulo"><br>

            <label for="descricao">Descrição:</label>
            <input type="text" name="descricao" id="descricao"><br>
            
            <label for="conteudo">Conteudo:</label><br>
            
            <textarea name="conteudo" id="conteudo" onkeypress="verificarEnter(event)"></textarea><br>
            <button type="submit">publicar</button>
        </form>

        <div id="previl"></div>
            
    <?php } ?>
    
    </div>

<?php
    global $conn;

    $query = "SELECT * FROM publicacoes;";

    $result = mysqli_query($conn,$query);
?>
    <div class="list-posts" id="list-posts">
        <?php while($dados = mysqli_fetch_assoc($result)){ ?>

        <a href="publicacao.php?id=<?=$dados['id_publi']?>"><?=$dados['titulo']?></a><br>
        <?php } ?>
    </div>

    
        

    
    <script src="script.acess-admin.js"></script>
    <!--trumbowyg editor-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="dist/trumbowyg.min.js"></script>
    <script type="text/javascript" src="dist/langs/pt_br.min.js"></script>
        <script>
            $('#conteudo').trumbowyg({
                btns: [
                    ['viewHTML'],
                    ['undo', 'redo'], // Only supported in Blink browsers
                    ['formatting'],
                    ['strong', 'em', 'del'],
                    ['superscript', 'subscript'],
                    ['link'],
                    ['insertImage'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['horizontalRule'],
                    ['removeformat'],
                    ['fullscreen']
                ]
            });         
        </script>
        
</body>
</html>


















<?php
    }else{
        echo "fazer login";
    }
?>