<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductoListado</title>
    <?php require 'Funciones/db_tiendas.php' ?>
    <?php require 'Objetos/Producto.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .enlaces, caption, th, td{
            text-align: center;
        }
        .table{
            width: 70%;
            margin: auto;
        }
    </style>
</head>
<body>
    <?php
        session_start();
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]!= "invitado"){
            $usuario = $_SESSION["usuario"];
            $rol = $_SESSION["rol"];
            $cesta = $_SESSION["idCesta"];
            
        } else{
            $_SESSION["usuario"] = "invitado";
            header('location: productosListado.php');
        }

        $sql = "SELECT p.idProducto, p.nombreProducto, p.precio, p.descripcion, pc.cantidad, p.imagen
            FROM productos p
            JOIN productosCestas pc
            ON p.idProducto = pc.idProducto
            WHERE pc.idCesta = '$cesta'";
        
        $resultado = $conexion -> query($sql);
        $productos = [];

        while($prod = $resultado -> fetch_assoc()){
            $id_producto = $prod["idProducto"];
            $nombreProducto = $prod["nombreProducto"];
            $precio = $prod["precio"];
            $descripcion = $prod["descripcion"];
            $cantidad = $prod["cantidad"];
            $imagen = $prod["imagen"];

            $nuevo_producto = new Producto($id_producto, $nombreProducto, $precio, $descripcion, $cantidad, $imagen);
            array_push($productos, $nuevo_producto);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $idProducto = $_POST["idProducto"];
            $unidad_temp = $_POST["unidad"];
            $unidadesPermitidas = ['1', '2', '3', '4', '5'];
            if (!isset($unidad_temp) || !in_array($unidad_temp, $unidadesPermitidas)){
                $err_unidad = "No hay nada gratis por aqui";
            } else{
                $compruebaCantidad = "SELECT cantidad FROM productosCestas 
                    WHERE idProducto = '$idProducto' AND idCesta = '$cesta'";
                $cantidadCesta = $conexion->query($compruebaCantidad);
                $fila = $cantidadCesta->fetch_assoc();
                $cantidadUnidades = $fila["cantidad"];

                if ($cantidadUnidades < $unidad_temp){
                    $err_unidad = "No tienes tantas unidades en la cesta";
                }else{
                    $unidad = $unidad_temp;
                }
            }
        }
    ?>
    <div class="enlaces">
        <a class="btn btn-primary" href="Funciones/cerrarSesion.php">Cerrar Sesion</a>
        <a class="btn btn-primary" href="productosListado.php">Seguir Comprando</a>
    </div>
    <table  class="table table-secondary table-hover table-striped">
        <caption class="table caption-top"><h1>Cesta de <?php echo $usuario ?></h1></caption>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Descripcion</th>
                <th>Imagen</th>
                <th>Quitar de Cesta</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($productos as $prod){
                    echo "<tr>";
                    echo "<td>" . $prod -> id_producto . "</td>";
                    echo "<td>" . $prod -> nombreProducto . "</td>";
                    echo "<td>" . $prod -> precio . "</td>";
                    echo "<td>" . $prod -> cantidad . "</td>";
                    echo "<td>" . $prod -> descripcion . "</td>";
                    ?>
                    <td><img height="70" src="<?php echo $prod -> imagen ?>" alt=""></td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="idProducto" value="<?php echo $prod -> id_producto ?>">
                            <select id="unidad" name="unidad">
                                <option value="1" selected>1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                            <input class="btn btn-primary" type="submit" value="Quitar de Cesta">
                        </form>
                    </td>
                    <?php
                    echo "</tr>";
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "7">Precio Total: </td>
            </tr>
        </tfoot>
    </table>
    <?php if(isset($err_unidad)) echo $err_unidad ?>
    <br>
    <?php
        if(isset($unidad)){
            $unidadesNuevas = $cantidadUnidades - $unidad;
            //Hemos comprobado antes que no pueda ser negativo haciendo que no se pueda
            //coger una cantidad de unidades a restar mayor a la cantidad total
            if ($unidadesNuevas == 0){
                $sql = "DELETE FROM productosCestas 
                    WHERE idProducto = '$idProducto' AND idCesta = '$cesta'";
            } else{
                $sql = "UPDATE productosCestas SET cantidad = '$unidadesNuevas' 
                    WHERE idProducto = '$idProducto' AND idCesta = '$cesta'";
            }
            $conexion -> query($sql);

            $compruebaStock = "SELECT cantidad FROM productos 
                WHERE idProducto = '$idProducto'";
            $cantidadDisponible = $conexion->query($compruebaStock);
            $fila = $cantidadDisponible->fetch_assoc();
            $cantidadStock = $fila["cantidad"];
            $nuevoStock = $cantidadStock + $unidad;

            $sql = "UPDATE productos SET cantidad = '$nuevoStock' 
                WHERE idProducto = '$idProducto'";
            $conexion -> query($sql);
            ?>
            <script>window.location.href = "cesta.php";</script>
            <?php
        } 
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>