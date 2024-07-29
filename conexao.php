<?php
    date_default_timezone_set('America/Recife');

    $server = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'blog';

    // Conexão utilizando mysqli orientado a objetos
    $conn = new mysqli($server,$user,$pass,$db); 

    // tratamento de err
    if($conn->connect_error){
        die('Erro na Conexão' . $conn->connect_error);
    }
?>