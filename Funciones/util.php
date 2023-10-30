<?php
    function depurar(string $entrada) : string{
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        return $salida;
    }
?>