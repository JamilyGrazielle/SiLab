<?php

    $hostname = "localhost";
    $username  = "root";
    $password = "";
    $bdname = "silab";



    $mysqli = new mysqli($hostname, $username, $password, $bdname);

    if ($mysqli->connect_errno){
        
    }
?>
