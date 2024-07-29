<?php
header('Content-Type: application/json');

include_once('../conexao.php');

if (!$conn) {
    echo json_encode(array('status' => 'error','dados'=>'Database connection failed'));
    exit;
}

$response = [];

if (isset($_GET['page']) && !empty($_GET['page']) && isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['max_com']) && !empty($_GET['max_com']) && isset($_GET['usuario_id']) && !empty($_GET['usuario_id'])) {
    $id_publi = $_GET['id'];
    $page = $_GET['page'];
    $user = $_GET['usuario_id'];

    $max_com = 10; //numero máximo de comentários
    $ofs = ($page * $max_com) - $max_com;

    $query_com = "SELECT * FROM comentarios WHERE id_publi_com=$id_publi ORDER BY data_comentario DESC LIMIT $max_com OFFSET $ofs";

    $result_comentarios = mysqli_query($conn, $query_com);

    if (mysqli_num_rows($result_comentarios)) {
        
        $dados = array();
        $i = 0;

        while($comentarios = mysqli_fetch_assoc($result_comentarios)){
            $com = array();

            $id_user_com = $comentarios['id_usuario_com'];
            $user_com = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM usuario WHERE id_usuario=$id_user_com"));

            $com['nome'] = $user_com['nome'];
            $com['email'] = $user_com['email'];

            $directory = 'users/user' . $user_com['id_usuario'] . '/';
            $foto_perfil = '../assets/images/foto-perfil-padrao.png'; //foto padrão

            if (is_dir($directory)) {
                $png_file = $directory . 'foto-perfil.png';
                $jpg_file = $directory . 'foto-perfil.jpg';

                if (file_exists($png_file)) {
                    $foto_perfil = '../' . $png_file;
                } elseif (file_exists($jpg_file)) {
                    $foto_perfil = '../' . $jpg_file;
                }
            }

            $com['foto_perfil'] = $foto_perfil;
            
            // Definir o fuso horário padrão
            date_default_timezone_set('America/Sao_Paulo');

            // Data do comentário obtida do banco de publicacao
            $data_comentario = $comentarios['data_comentario']; // Exemplo

            // Verificar se a data do comentário está definida e é válida
            if (isset($data_comentario) && !empty($data_comentario)) {
                // Data atual
                $data_atual = date('Y-m-d H:i:s');

                // Criar objetos DateTime
                $datetime_comentario = new DateTime($data_comentario);
                $datetime_atual = new DateTime($data_atual);
                
                // Calcular a diferença entre as datas
                $intervalo = $datetime_comentario->diff($datetime_atual);

                // Converter a diferença para minutos
                $minutos = ($intervalo->y * 365 * 24 * 60) + 
                                ($intervalo->m * 30 * 24 * 60) + 
                                ($intervalo->d * 24 * 60) + 
                                ($intervalo->h * 60) + 
                                $intervalo->i;

                // Determinar a unidade de tempo apropriada para exibição
                if ($minutos >= 60 && $minutos < 1440) {
                    $horas = round($minutos / 60);
                    $str_tempo_publi_com =  "Há " . $horas . ($horas > 1 ? " horas" : " hora");
                } elseif ($minutos >= 1440 && $minutos < 10080) {
                    $dias = round($minutos / 1440);
                    $str_tempo_publi_com = "Há " . $dias . ($dias > 1 ? " dias" : " dia");
                } elseif ($minutos >= 10080 && $minutos < 43200) {
                    $semanas = round($minutos / 10080);
                    $str_tempo_publi_com = "Há " . $semanas . ($semanas > 1 ? " semanas" : " semana");
                } elseif ($minutos >= 43200 && $minutos < 525600) {
                    $meses = round($minutos / 43200);
                    $str_tempo_publi_com = "Há " . $meses . ($meses > 1 ? " meses" : " mês");
                } elseif ($minutos >= 525600) {
                    $anos = round($minutos / 525600);
                    $str_tempo_publi_com = "Há " . $anos . ($anos > 1 ? " anos" : " ano");
                } else {
                    $str_tempo_publi_com = "Há $minutos minutos";
                }
            } else {
                $str_tempo_publi_com =  "Data do comentário inválida.";
            }    

            $com['tempo_publi'] = $str_tempo_publi_com;
        
            if($user === false) {
                $com['usuario_log'] = false;              
            }else{
                $com['com_usuario_log'] = $comentarios['id_usuario_com'] == $user ? true : false;   
            }   

            $com['texto'] = $comentarios['texto'];
            $com['id_publi'] = $comentarios['id_publi_com'];
            $com['id_usuario'] = $comentarios['id_usuario_com'];
            $com['id_comentario'] = $comentarios['id_comentario'];

            $dados[$i] = $com;
            $i++;
        } 


        //$comentarios = mysqli_fetch_all($result_comentarios, MYSQLI_ASSOC);
        echo json_encode(array('status' => 'sucess','dados' => $dados));

        // var_dump($comentarios);
    } else {
        echo json_encode(array('status' => 'vazio', 'dados'=>'Ainda não há comentários'));
    }
} else {
    echo json_encode(array('status' => 'error','dados'=>'Invalid parameters'));
}
?>