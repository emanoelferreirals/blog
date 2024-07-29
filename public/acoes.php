<?php
    require_once('../conexao.php');
    session_start();

    if(isset($_POST['operacao']) && !empty($_POST['operacao'])){
        $oper = $_POST['operacao'];

        switch($oper){
            case 'login': login();  break;
            case 'cadastro': cadastro(); break;
            case 'deslogar': removeLogin(); break;
            case 'deslogar-adm': removeLogin(); break;
            case 'salve-publi': salvarPublicacao(); break;
            case 'remove-publi': removePublicacao(); break;
            case 'comentario': comentario(); break;
            case 'upload-foto-perfil': uploadFotoPerfil(); break;
            case 'delete-comentario': deleteComentario();break;
            case 'curtir-publi': curtirPubli();break;
            case 'carregar-posts': carregarPosts();break;
            default: echo "não coreespondente";
        }
    }

    function carregarPosts(){
        if(isset($_POST['page']) && !empty($_POST['page']) && 
        isset($_POST['max_com']) && !empty($_POST['max_com'])){
            $page = $_POST['page'];
            $max_com = $_POST['max_com'];

            global $conn;
            
            $offset = ($page * $max_com) - $max_com;

            $query = "SELECT * FROM publicacoes ORDER BY data_publicacaO DESC LIMIT ? OFFSET ?;";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii',$max_com, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            $i = 0;

            if($result->num_rows>0){

                $dados = array();

                while($post = $result->fetch_assoc()){
                    $data = new DateTime($post['data_publicacao']);
                    $data_formatada = $data->format('d/m/Y');
                    $hora_formatada = $data->format('H:i');

                    $dados[$i] = array(
                        'id'=>$post['id_publi'], 
                        'titulo'=>$post['titulo'],
                        'descricao'=>$post['descricao'],
                        'data_publi'=>$data_formatada . ' às ' . $hora_formatada
                    );

                    $i++;
                }

                echo json_encode(array('sucess' => true,'num_rows' => $result->num_rows ,'dados' => $dados));
            }else{
                echo json_encode(array('sucess' => false, 'dados'=> 'vazio'));
            }
        }else{
            echo json_encode(array('sucess' => false, 'dados'=> 'campos vazios'));
        }
    }

    function login() {
        global $conn;

        $_SESSION['login-admin'] = false;
    
        if(isset($_POST['email-login']) &&
        isset($_POST['senha-login']) && 

        !empty($_POST['email-login']) && 
        !empty($_POST['senha-login'])) {
    
            $email = $_POST['email-login'];
            $senha = $_POST['senha-login'];

            $query_login_admin = "SELECT * FROM usuario_admin WHERE email='$email';";
            $result_login_admin = mysqli_query($conn,$query_login_admin);

            if($result_login_admin && mysqli_num_rows($result_login_admin)>0){
                $result_dados_admin = mysqli_fetch_assoc($result_login_admin);

                if($senha == $result_dados_admin['senha'] ){
                    $_SESSION['login-admin'] = true;
                    header("Location: home/");
                }else{
                    $_SESSION['login-admin'] = false;
                    echo "Senha e/ou email errado(s)";
                }
            }else{
                $query_login = "SELECT * FROM usuario WHERE email='$email';";
        
                $result_login = mysqli_query($conn, $query_login);
                
                if($result_login && mysqli_num_rows($result_login) > 0) {

                    $dadosUsuario = mysqli_fetch_assoc($result_login);

                    $senhaDoBanco = $dadosUsuario['senha'];
                    $emailDoBanco = $dadosUsuario['email'];
                
                    if($emailDoBanco == $email && password_verify($senha,$senhaDoBanco)) {
                        $_SESSION['login'] = true;
                        $_SESSION['usuario_id'] = $dadosUsuario['id_usuario'];

                        if(isset($_POST['redirect']) && !empty($_POST['redirect'])){
                            header("Location: ".$_POST['redirect']);
                            exit;
                        }else{
                            header("Location: home/");
                            exit;
                        }
                    } else {
                        echo "Usuário e/ou senha errados";
                        header("Location: login/?error=4");
                        exit;
                    }
                }else{
                    //erro 5
                    echo "Você ainda não tem uma conta";
                }
            }
        }
    }
    


    function cadastro(){
        global $conn;
    
        if(isset($_POST['user-cadastro']) && 
        isset($_POST['email-cadastro']) && 
        isset($_POST['senha-cadastro']) && 
        isset($_POST['confirme-senha']) &&

        !empty($_POST['user-cadastro']) && 
        !empty($_POST['email-cadastro']) && 
        !empty($_POST['senha-cadastro']) && 
        !empty($_POST['confirme-senha'])){

            $user = $_POST['user-cadastro'];
            $email = $_POST['email-cadastro'];
            $senha = $_POST['senha-cadastro'];
            $confirmeSenha = $_POST['confirme-senha'];
    
            if(strcmp($senha,$confirmeSenha)==0){

                $query_consult = "SELECT * FROM usuario WHERE email='$email';";

                $result_consult = mysqli_query($conn,$query_consult);

                if($result_consult && mysqli_num_rows($result_consult) > 0){
                     $email_disponivel = false;
                }else{
                    $email_disponivel = true;
                }

                /*while($dados_consult=mysqli_fetch_assoc($result_consult)){
                    $email_disponivel = $dados_consult['email']==$email?false:true;
                    if($email_disponivel==false)
                        break;
                }*/

                if($email_disponivel){
                        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                        $query = "INSERT INTO usuario (id_usuario,nome,email,senha) VALUES (DEFAULT,'$user','$email','$senhaHash');";


                    $result = mysqli_query($conn,$query);

                    $id_user = mysqli_insert_id($conn);
                    
                    if($result) {
                        if(!is_dir('user'.$id_user)){
                            $diretorio_user = 'users';
                            $novo_diretorio = $diretorio_user . DIRECTORY_SEPARATOR . 'user'.$id_user;
                            if($novo_diretorio){
                                mkdir($novo_diretorio);
                                if(is_dir($novo_diretorio)){
                                    header("Location: login/"); exit;
                                }
                            }
                        }else{
                            echo "Este diretorio já existe";
                        }
                    } else {
                        header("Location: login/?error=3");
                        exit;
                    }
                }else{
                    header("Location: login/?error=5");
                    exit;
                }
   
            } else {
                header("Location: login/?error=4");
                exit;
            }
    
        } else {
            header("Location: login/?error=2");
            exit;
        }
    }

    function uploadFotoPerfil() {
        if (isset($_FILES['foto-perfil'])) {
            $usuario = $_SESSION['usuario_id'];
    
            // Verifica se houve erro no upload
            if ($_FILES['foto-perfil']['error'] !== UPLOAD_ERR_OK) {
                echo 'Erro no upload do arquivo.';
                return;
            }
    
            // Recolhendo informações do arquivo
            $nomeArquivo = $_FILES['foto-perfil']['name'];
            $tamanhoArquivo = $_FILES['foto-perfil']['size'];
            $extensaoArquivo = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
            $arquivoTemporario = $_FILES['foto-perfil']['tmp_name'];
    
            // Limites de tamanho e tipos permitidos
            $maxTamanho = 5 * 1024 * 1024; // 5 megabytes
            $tiposPermitidos = array('jpg', 'jpeg', 'png');
    
            // Verifica o tamanho do arquivo
            if ($tamanhoArquivo > $maxTamanho) {
                echo 'O tamanho do arquivo é muito grande. O tamanho máximo permitido é de 5MB.';
                return;
            }
    
            // Verifica a extensão do arquivo
            if (!in_array($extensaoArquivo, $tiposPermitidos)) {
                echo 'Apenas arquivos JPG, JPEG e PNG são permitidos.';
                return;
            }
    
            // Diretório do usuário
            $diretorioUsuario = "users/user$usuario/";
    
            // Cria o diretório se não existir
            if (!is_dir($diretorioUsuario)) {
                if (!mkdir($diretorioUsuario, 0777, true)) {
                    echo 'Erro ao criar o diretório do usuário.';
                    return;
                }
            }
    
            // Limpa o diretório do usuário
            $arquivosUsuario = glob($diretorioUsuario . '*');
            foreach ($arquivosUsuario as $arquivo) {
                unlink($arquivo);
            }
    
            // Move o arquivo para o diretório do usuário
            $caminhoArquivo = $diretorioUsuario . "foto-perfil.$extensaoArquivo";
            if (move_uploaded_file($arquivoTemporario, $caminhoArquivo)) {
                header('Location: account/');
                exit;
            } else {
                echo 'Erro ao mover o arquivo para o diretório de destino.';
                header('Location: account/');
            }
        } else {
            echo 'Nenhum arquivo selecionado.';
            
        }
    }
    


    function salvarPublicacao(){
        if(isset($_SESSION['login']) && $_SESSION['login']==true) {
            if(isset($_POST['id_salve_publi']) && 
            !empty($_POST['id_salve_publi'])) {
                $id_publi = $_POST['id_salve_publi'];
                $id_user = $_SESSION['usuario_id'];

                global $conn;

                //echo $id_publi ." + " . $id_user; 
                
                $query = "INSERT INTO publicacoes_salvas (id_user_s, id_publi_s) VALUES ($id_user, $id_publi);";

                $result = mysqli_query($conn,$query);

                if($result){
                    header('Location: home/');
                    exit;
                }else{
                    header('Location: home/?erro=3');
                    exit;
                }

            }else{
                header('Location: home/?erro=3');
                exit;
            }
        }else{
            header('Location: login/');
            exit;
        }
    }


        function removePublicacao(){
            if(isset($_SESSION['login']) && $_SESSION['login']==true) {
                if(isset($_POST['id_salve_publi']) && 
                !empty($_POST['id_salve_publi'])) {
                    $id_publi = $_POST['id_salve_publi'];
                    $id_user = $_SESSION['usuario_id'];

                    global $conn;

                    echo $id_publi ." + " . $id_user; 
                    
                    $query = "DELETE FROM publicacoes_salvas WHERE id_user_s='$id_user' AND id_publi_s='$id_publi';";

                    $result = mysqli_query($conn,$query);

                    if($result){
                        header('Location: account/');
                        exit;
                    }else{
                        header('Location: account/?erro=3');
                        exit;
                    }

            }else{
                header('Location: account/?erro=2');
                exit;
            }
        }else{
            header('Location: account/?erro=1');
            exit;
        }
    }

    function comentario() {
        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
            if (isset($_POST['id_publi_com']) && 
                !empty($_POST['id_publi_com']) &&
                isset($_POST['comentario']) &&
                !empty($_POST['comentario'])) {
                
                $id_publi = $_POST['id_publi_com'];
                $id_user = $_SESSION['usuario_id'];
                $comentario = $_POST['comentario'];
    
                global $conn;
                
                $query = "INSERT INTO comentarios (id_usuario_com, id_publi_com, texto, data_comentario) VALUES ($id_user, $id_publi,'$comentario',NOW());";
    
                $result = mysqli_query($conn, $query);
    
                if ($result) {
                    $query_num_com = "SELECT * FROM comentarios WHERE id_publi_com=$id_publi;";
                    $result_num_com = mysqli_query($conn,$query_num_com);
    
                    $num_com = mysqli_num_rows($result_num_com);
                    echo json_encode(['success' => true,'num_com'=>$num_com]);
                    
                } else {
                    echo json_encode(['success' => false, 'error' => 'Erro ao inserir comentário no banco de dados.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Dados do comentário incompletos.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Usuário não está logado.','action'=>'login']);
        }
        
    }
    

    function deleteComentario(){
        if(isset($_POST['id-comentario']) && !empty($_POST['id-comentario'])){
            $id_comentario = $_POST['id-comentario'];

            global $conn;

            $query = "DELETE FROM comentarios WHERE id_comentario='$id_comentario'";

            $result = mysqli_query($conn,$query);

            if($result){
                //header('Location: publicacao/?id='.$_POST['redirecionamento']);
                echo json_encode(['sucess' => true]);

            }else{
                //header('Location: publicacao/?id='.$_POST['redirecionamento'].'&error=6');
                echo json_encode(['error' => false,'dados'=>'Erro na execução']);
            }
        }else{
            echo json_encode(['error' => false, 'dados'=>'id vazio']);
        }
    }

    function curtirPubli(){
        if(isset($_POST['id_publi']) && !empty($_POST['id_publi'])){
            $id_publi = $_POST['id_publi']; 

            global $conn;

            if(isset($_SESSION['usuario_id'])){
                $id_user = $_SESSION['usuario_id'];

                $query_consult = "SELECT * FROM curtidas WHERE id_user_curt=$id_user AND id_publi_curt=$id_publi";
                $result_consult = mysqli_query($conn, $query_consult);

                if(mysqli_num_rows($result_consult)>0){
                    $query = "DELETE FROM curtidas WHERE id_user_curt=$id_user AND id_publi_curt=$id_publi;";
                    //echo 0;//retorna publicação não curtida
                }else{
                    $query = "INSERT INTO curtidas (id_user_curt,id_publi_curt) VALUES ($id_user, $id_publi);";
                    //echo 1; //retorna publicação curtida
                }
 
                $result = mysqli_query($conn, $query);

                $query_num_curtidas = "SELECT * FROM curtidas WHERE id_publi_curt=$id_publi";
                $result_num_curtidas = mysqli_query($conn,$query_num_curtidas);
                $num_curtidas = mysqli_num_rows($result_num_curtidas);

                echo $num_curtidas;
                            

            }else{
                echo "login";
            }

            
                


        }
    }

    function removeLogin(){
        session_destroy();
        header('Location: home/');
        exit;
    }




    /**
     * ERROS:
     * 
     * 1: LOGIN
     * 2: CAMPOS VAZIOS
     * 3: FALHA AO EXECUTAR NO BD
     * 4: DADOS INCORRETOS
     * 5: EMAIL NÃO DISPONIVEL
     */
    
?>