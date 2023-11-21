<?php
    session_start();
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]!= "invitado"){
        $usuario = $_SESSION["usuario"];
        $rol = $_SESSION["rol"];
        $cesta = $_SESSION["idCesta"];
        
    } else{
        $_SESSION["usuario"] = "invitado";
        header('location: ../productosListado.php');
    }

    

    echo "holap";
?>