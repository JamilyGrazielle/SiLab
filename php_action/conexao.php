<?php

    $hostname = "localhost";
    $username  = "root";
    $password = "";
    $bdname = "silab";



    $mysqli = new mysqli($hostname, $username, $password, $bdname);

    if ($mysqli->connect_errno){
        echo "Falha ao conectar:(" . $mysqli->connect_errno . ")" . $mysqli->connect_errno;
    }
    else{
        echo "Conexão concluida"; 
    }
?>
