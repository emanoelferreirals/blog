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

            $query = "SELECT * FROM publicacoes ORDER BY data_publicacao DESC LIMIT ? OFFSET ?;";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii',$max_com, $offset);
            $stmt->execute();
            $result = $stmt->get_result();  


            //contando número de publicações
            $result_num_publi = mysqli_query($conn,'SELECT * FROM publicacoes');
            $num_publi = mysqli_num_rows($result_num_publi);

            //Definindo número de pages
            $num_pages = round($num_publi/$max_com);

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

                echo json_encode(array('sucess' => true,'total_posts' => $num_publi,'num_pages' => $num_pages ,'dados' => $dados));
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
    
        if (isset($_POST['email-login']) && isset($_POST['senha-login']) &&
            !empty($_POST['email-login']) && !empty($_POST['senha-login'])) {
    
            $email = $_POST['email-login'];
            $senha = $_POST['senha-login'];
    
            $query_login_admin = "SELECT * FROM usuario_admin WHERE email = ?";
            $stmt = $conn->prepare($query_login_admin);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result_login_admin = $stmt->get_result();
    
            if ($result_login_admin && $result_login_admin->num_rows > 0) {
                $result_dados_admin = $result_login_admin->fetch_assoc();
    
                if ($senha == $result_dados_admin['senha']) {
                    $_SESSION['login-admin'] = true;
                    header("Location: home/");
                } else {
                    $_SESSION['login-admin'] = false;
                    echo "Senha e/ou email errado(s)";
                }
            } else {
                $query_login = "SELECT * FROM usuario WHERE email = ?";
                $stmt = $conn->prepare($query_login);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result_login = $stmt->get_result();
    
                if ($result_login && $result_login->num_rows > 0) {
                    $dadosUsuario = $result_login->fetch_assoc();
                    $senhaDoBanco = $dadosUsuario['senha'];
    
                    if (password_verify($senha, $senhaDoBanco)) {
                        $_SESSION['login'] = true;
                        $_SESSION['usuario_id'] = $dadosUsuario['id_usuario'];
    
                        if (isset($_POST['redirect']) && !empty($_POST['redirect'])) {
                            header("Location: " . $_POST['redirect']);
                            exit;
                        } else {
                            header("Location: home/");
                            exit;
                        }
                    } else {
                        echo "Usuário e/ou senha errados";
                        header("Location: login/?error=4");
                        exit;
                    }
                } else {
                    echo "Você ainda não tem uma conta";
                }
            }
        }
    }


    function cadastro() {
        global $conn;
    
        if (isset($_POST['user-cadastro']) && isset($_POST['email-cadastro']) &&
            isset($_POST['senha-cadastro']) && isset($_POST['confirme-senha']) &&
            !empty($_POST['user-cadastro']) && !empty($_POST['email-cadastro']) &&
            !empty($_POST['senha-cadastro']) && !empty($_POST['confirme-senha'])) {
    
            $user = $_POST['user-cadastro'];
            $email = $_POST['email-cadastro'];
            $senha = $_POST['senha-cadastro'];
            $confirmeSenha = $_POST['confirme-senha'];
    
            if (strcmp($senha, $confirmeSenha) == 0) {
                $query_consult = "SELECT * FROM usuario WHERE email = ?";
                $stmt = $conn->prepare($query_consult);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result_consult = $stmt->get_result();
    
                $email_disponivel = $result_consult->num_rows == 0;
    
                if ($email_disponivel) {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $query = "INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('sss', $user, $email, $senhaHash);
    
                    if ($stmt->execute()) {
                        $id_user = $conn->insert_id;
    
                        $novo_diretorio = 'users' . DIRECTORY_SEPARATOR . 'user' . $id_user;
                        if (!is_dir($novo_diretorio)) {
                            mkdir($novo_diretorio, 0777, true);
                            if (is_dir($novo_diretorio)) {
                                header("Location: login/");
                                exit;
                            }
                        } else {
                            echo "Este diretório já existe";
                        }
                    } else {
                        header("Location: login/?error=3");
                        exit;
                    }
                } else {
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
    
    function salvarPublicacao() {
        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
            if (isset($_POST['id_salve_publi']) && !empty($_POST['id_salve_publi'])) {
                $id_publi = $_POST['id_salve_publi'];
                $id_user = $_SESSION['usuario_id'];
    
                global $conn;
    
                $query = "INSERT INTO publicacoes_salvas (id_user_s, id_publi_s) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $id_user, $id_publi);
    
                if ($stmt->execute()) {
                    header('Location: home/');
                    exit;
                } else {
                    header('Location: home/?erro=3');
                    exit;
                }
            } else {
                header('Location: home/?erro=3');
                exit;
            }
        } else {
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
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('ii', $id_user, $id_publi);

                    if($stmt->execute()){
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
                
                $query = "INSERT INTO comentarios (id_usuario_com, id_publi_com, texto, data_comentario) VALUES (?, ?,?,NOW());";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iis',$id_user, $id_publi, $comentario);
    
                if ($stmt->execute()) {
                    $query_num_com = "SELECT * FROM comentarios WHERE id_publi_com=$id_publi;";
                    $result_num_com = mysqli_query($conn,$query_num_com);
    
                    $num_com = mysqli_num_rows($result_num_com);
                    echo json_encode(['success' => true,'num_com'=>$num_com, 'dados' =>' $dados']);
                    

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

            $query = "DELETE FROM comentarios WHERE id_comentario=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i',$id_comentario);

            if($stmt->execute()){
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