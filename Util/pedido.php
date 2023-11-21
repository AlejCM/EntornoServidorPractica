<?php require 'db_tiendas.php' ?>
<?php
    session_start();
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]!= "invitado"){
        $usuario = $_SESSION["usuario"];
        $rol = $_SESSION["rol"];
        $cesta = $_SESSION["idCesta"];
        
    } else{
        $_SESSION["usuario"] = "invitado";
        header('location: ../Views/productosListado.php');
    }

    /* Coge el precio total */
    $compruebaPrecio = "SELECT precioTotal FROM cestas
    WHERE idCesta = '$cesta'";
    $resultadoPrecio = $conexion->query($compruebaPrecio);
    $filaPrecio = $resultadoPrecio->fetch_assoc();
    $precioTotal = $filaPrecio["precioTotal"];

    /* Crea el pedido */
    $sql = "INSERT INTO pedidos (usuario, precioTotal)
        VALUES ('$usuario', '$precioTotal')";
    $conexion -> query($sql);

    /* Coge el id del pedido */
    $compruebaId = "SELECT LAST_INSERT_ID()";
    $resultadoId = $conexion->query($compruebaId);
    $filaId = $resultadoId->fetch_assoc();
    $idPedido = $filaId["LAST_INSERT_ID()"];

    /* Saca los productos de la cesta */
    $sql = "SELECT * FROM productosCestas WHERE idCesta = '$cesta'";
    $resultado = $conexion -> query($sql);
    $productos = [];
    $cont = 1;

    while($prod = $resultado -> fetch_assoc()){
        $id_producto = $prod["idProducto"];
        $cantidad = $prod["cantidad"];
        
        $compruebaPrecioUni = "SELECT precio FROM productos
            WHERE idProducto = '$id_producto'";
        $resultadoPrecioUni = $conexion->query($compruebaPrecioUni);
        $filaPrecioUni = $resultadoPrecioUni->fetch_assoc();
        $precio = $filaPrecioUni["precio"];

        /* Crea la linea de pedido */
        $sql = "INSERT INTO lineasPedidos (lineaPedido, idProducto, idPedido, precioUnitario, cantidad)
            VALUES ('$cont', '$id_producto', '$idPedido', '$precio', '$cantidad')";
        $conexion -> query($sql);

        /* Elimina el producto de la cesta */
        $sql = "DELETE FROM productosCestas 
            WHERE idProducto = '$id_producto' AND idCesta = '$cesta'";
        $conexion -> query($sql);

        $sql = "UPDATE cestas SET precioTotal = 0 
        WHERE idCesta = '$cesta'";
        $conexion -> query($sql);

        $cont++;
    }

    header('location: ../Views/cesta.php');
?>