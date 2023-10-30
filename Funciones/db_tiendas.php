<?php
    $b_servidor = "localhost";
    $b_usuario = "root";
    $b_contrasena = "medac";
    $b_base_de_datos = "tiendas";

    $conexion = new Mysqli($b_servidor, $b_usuario, $b_contrasena, $b_base_de_datos)
        or die("Error de conexion");

?>