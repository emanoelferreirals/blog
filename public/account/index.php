<?php
    session_start();

    if(isset($_SESSION['login']) && $_SESSION['login']==true){
        $id_user = $_SESSION['usuario_id'];

        require_once("../../conexao.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta</title>
    <link rel="stylesheet" href="../assets/css/conta-user.css">
    <link rel="stylesheet" href="../assets/css//header.css">
    <script src="../assets/js/header.js" defer></script>
</head>

<body class="texto theme-background">
    <?php include('../includes/header.php');?>

    <div class="container texto theme-background">
        <form action="../acoes.php" method="post">
            <input type="hidden" name="operacao" value="deslogar">
            <button type="submit" class="buttom"><= Sair da conta</button>
        </form>
        <h1>Dados</h1>
        <div class="dados-pessoais">
            <?php
                $query = "SELECT * FROM usuario WHERE id_usuario = $id_user";
                $result_user = mysqli_query($conn,$query);
                if($result_user){
                    $dados_usuario = mysqli_fetch_assoc($result_user);
                    $id = $dados_usuario['id_usuario'];
                    $nome = $dados_usuario['nome'];
                    $email = $dados_usuario['email'];
                    $senha = $dados_usuario['senha'];
                    $directory = '../users/user' . $id . '/';
                    $foto_perfil = '../assets/images/foto-perfil-padrao.png';
                    if (is_dir($directory)) {
                        $png_file = $directory . 'foto-perfil.png';
                        $jpg_file = $directory . 'foto-perfil.jpg';
                        if (file_exists($png_file)) {
                            $foto_perfil = $png_file;
                        } elseif (file_exists($jpg_file)) {
                            $foto_perfil = $jpg_file;
                        }
                    }
            ?>
            <div id="foto-perfil" style="background-image: url('<?=$foto_perfil?>');"></div>
            <button id="btn-mudar-foto" class="buttom">Mudar Foto</button>
            <p id="nome"><?=$nome?></p>
            <p id="email"><?=$email?></p>
            <?php } ?>
        </div>
        <?php
            $query = "SELECT * FROM publicacoes_salvas WHERE id_user_s = $id_user";
            $result = mysqli_query($conn,$query);
            while($salvas = mysqli_fetch_assoc($result)){
                $id_publi_salva = $salvas['id_publi_s'];
                $query_consult_publi = "SELECT * FROM publicacoes WHERE id_publi = $id_publi_salva";
                $consult_publi = mysqli_query($conn,$query_consult_publi);
                while($dados_consult = mysqli_fetch_assoc($consult_publi)){ ?>
                    <h1><?=$dados_consult['titulo']?></h1>
                    <div id="conteudo<?=$dados_consult['id_publi'];?>">
                        <?=$dados_consult['descricao'];?>
                    </div>
                    <form action="../acoes.php" method="post">
                        <input type="hidden" name="operacao" value="remove-publi">
                        <input type="hidden" name="id_salve_publi" value="<?=$dados_consult['id_publi']; ?>">
                        <button type="submit" class="buttom">Remover publicação</button>
                    </form>
          <?php     }
            }
        }else{
            header("Location: login/");
            exit; // Encerra a execução do script após redirecionar
        } ?>
    </div>

    <div id="background-tela-escura" style="display: none;">
        <div id="area-mudar-foto" style="display: none;">
            <form action="../acoes.php" method="post" enctype="multipart/form-data" id="form-enviar-foto">
                <input type="hidden" name="operacao" value="upload-foto-perfil">
                <input type="file" name="foto-perfil" id="inp-foto-perfil">
                <div>
                    <button type="submit" id="btn-enviar-foto" class="buttom">Enviar</button>
                    <button type="button" id="cancelar-envio-foto" class="buttom">Cancelar</button>
                </div>
                <p id="erro"></p>
            </form>
        </div>
        <div id="foto-perfil-open">
        </div>
    </div>

    <script>
        const fotoPerfil = document.querySelector('#foto-perfil');
        const fotoPerfilOpen = document.querySelector('#foto-perfil-open');
        const formMudarFt = document.querySelector('#form-enviar-foto');
        const btnEnviarFt= document.querySelector('#btn-enviar-foto');
        const btnCancelarEnvioFt = document.querySelector('#cancelar-envio-foto');
        const btnOpenAreaMudar = document.querySelector('#btn-mudar-foto');
        const inpFoto = document.querySelector('#inp-foto-perfil');
        const backgroundTela = document.querySelector('#background-tela-escura');
        const openAreaMudar = document.querySelector('#area-mudar-foto');

        window.addEventListener('DOMContentLoaded',function(){
            btnOpenAreaMudar.addEventListener('click',()=>{
                openAreaMudarFt();

                formMudarFt.addEventListener('submit',(event)=>{
                    event.preventDefault();
                });

                btnEnviarFt.addEventListener('click',(event)=>{
                    if(validarUpload()){
                        formMudarFt.submit();
                    }else{
                        event.preventDefault();
                    }
                });

                btnCancelarEnvioFt.addEventListener('click',()=>{
                    closeAreaMudarFt();
                });

                backgroundTela.addEventListener('click',function(){
                    //closeAreaMudarFt();
                });
            });

            inpFoto.addEventListener('change',()=>{
                if(validarUpload()){
                    // Implementação futura
                    formMudarFt.submit(); 
                }
            });

            fotoPerfil.addEventListener('click',()=>{
                const caminhoImage = getComputedStyle(fotoPerfil).getPropertyValue('background-image');
                const imageUrl = caminhoImage.slice(5, -2);
                backgroundTela.style.display = 'flex';
                fotoPerfilOpen.style.display = 'flex';
                fotoPerfilOpen.style.backgroundImage = `url(${imageUrl})`;
                document.body.classList.add('modal-open');
                
                
                backgroundTela.addEventListener('click',()=>{
                    backgroundTela.style.display = 'none';
                    fotoPerfilOpen.style.display = 'none';
                    document.body.classList.remove('modal-open');
                });
            });


        });

        function validarUpload(){
            if (inpFoto.files.length > 0) {
                const file_size = inpFoto.files[0].size;
                const file_type = inpFoto.files[0].type;

                const maxZize = 5 * 1024 * 1024;
                const typesValidos = ['image/jpg', 'image/jpeg', 'image/png'];

                if(file_size > maxZize && !typesValidos.includes(file_type)){
                    window.alert("O arquivo é muito grande.O tamanho máximo é 5mb. Apenas arquivos JPG, JPEG e PNG são permitidos.");
                }else if(file_size > maxZize){
                    window.alert("O arquivo é muito grande.O tamanho máximo é 5mb");
                }else if(!typesValidos.includes(file_type)){
                    window.alert("Apenas arquivos JPG, JPEG e PNG são permitidos.");
                }else{
                    return true;
                }                
            }else{
                window.alert("Selecione algum arquivo");
                return false;
            }
        }

        function openAreaMudarFt(){
            backgroundTela.style.display = 'flex';
            openAreaMudar.style.display = 'flex';
            document.body.classList.add('modal-open');
        }

        function closeAreaMudarFt(){
            backgroundTela.style.display = 'none';
            openAreaMudar.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    </script>
</body>
</html>
