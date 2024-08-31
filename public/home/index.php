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
    <script src="../assets/js/home.js" defer></script>

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

        <main class="list-publi">
            <?php
                $dados = array(
                    'operacao' => 'carregar-posts',
                    'page' => 0,
                    'max_com'=>3
                );

                if(!isset($_GET['list']) || empty($_GET['list']) || $_GET['list'] < 1){
                    $dados['page'] = 1;
                }else{
                    $dados['page'] = $_GET['list'];
                }

                // Inicializa uma sessão cURL
                $ses_ch = curl_init('http://localhost/blog/public/acoes.php');
                
                // Configura cURL para enviar uma requisição POST
                curl_setopt($ses_ch, CURLOPT_POST, 1);
                curl_setopt($ses_ch, CURLOPT_POSTFIELDS, http_build_query($dados));

                // Configura cURL para retornar a resposta
                curl_setopt($ses_ch, CURLOPT_RETURNTRANSFER, true);

                // Executa a requisição e captura a resposta
                $resposta_json = curl_exec($ses_ch);

                // Verifica se houve algum erro
                if(curl_errno($ses_ch)) {
                    echo 'Erro: ' . curl_error($ses_ch);
                } else {
                    // Exibe a resposta
                    $resposta_obj = json_decode($resposta_json);
                    /*echo "<pre>";
                        var_dump($resposta_obj);
                    echo "</pre>";*/  
                    
                    // Total de postas para determinar o número da lista
                    $total_posts = $resposta_obj->total_posts;

                    //Calculo do número inicial da listagem da página
                    $ind_list = $total_posts - ($dados['page'] - 1) * $dados['max_com'];

                    if($resposta_obj->sucess){
                        foreach($resposta_obj->dados as $item){ ?>
                            <div class="publi">
                                <p  class='titulo-publi'>
                                    <?=$ind_list?>
                                    <a href='../publicacao/?id=<?=$item->id?>'>
                                        <?=$item->titulo?>
                                    </a>
                                </p>
                                <p class='subtitulo'>
                                    <?=$item->descricao?>
                                </p>
                                <div class="opcoes">
                                    <p class="data-publi">
                                        Publicado em <?=$item->data_publi?>  
                                    </p>
                                </div>
                            </div>
                            <br>                            

                        <?php $ind_list--; 
                
                        } ?><!--Fechamento do foreach-->

                            <div id='pagination'> 
                                <?php 
                                    echo "<br><br>";

                                    for($i = 1; $i <= $resposta_obj->num_pages; $i++){
                                        if($dados['page'] == $i){
                                            echo "<a href='?list=$i' class='selected btn-pagination'>" .$i. "</a>";
                                        }else{
                                            echo "<a href='?list=$i' class='btn-pagination'>" .$i. "</a>";
                                        }
                                    } 
                                ?> 
                            </div>
                        <?php
                    }
                }

                // Fecha a sessão cURL
                curl_close($ses_ch);
            ?>
        </main>

        <div id="pagination"></div>

    </div>

    <footer>
        &copy; emanoeldev | Direitos Reservados
    </footer>

    <!--Jquery-->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</body>
</html>


<!--TEMPLATE POSTS

    <div class='publi texto theme-background'>
        <span class="data-publi">${data_publi}</span>
        <h1 class='titulo-publi'>${titulo}</h1>
        <p class='subtitulo'>${descricao}</p>
        <a href='../publicacao?id=${id}' class='ver-mais texto'>Veja mais</a> 
    </div>

-->