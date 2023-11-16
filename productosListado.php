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
        th, td{
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
        session_start();
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]!= "invitado"){
            $usuario = $_SESSION["usuario"];

            $consulta = "SELECT rol FROM usuarios WHERE usuario='$usuario'";
            $resultado = $conexion->query($consulta);
            $fila = $resultado->fetch_assoc();
            $rol = $fila["rol"];
            $_SESSION["rol"] = $rol;

            $consultaCesta = "SELECT idCesta FROM cestas WHERE usuario='$usuario'";
            $resultadoCesta = $conexion->query($consultaCesta);
            $filaCesta = $resultadoCesta->fetch_assoc();
            $cesta = $filaCesta["idCesta"];
            $_SESSION["idCesta"] = $cesta;
            
            
        } else{
            $_SESSION["usuario"] = "invitado";
            $usuario = $_SESSION["usuario"];
        }

        echo "<h1>Bienvenido " . $usuario . "</h1>";

        $sql = "SELECT * FROM productos";
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
            if (isset($unidad_temp) && in_array($unidad_temp, $unidadesPermitidas)){
                $unidad = $unidad_temp;
            } else{
                $err_unidad = "No hay nada gratis por aqui";
            }
        }
    ?>
    <table  class="table table-secondary table-hover table-striped">
    <caption class="table caption-top"><h1>Listado Productos</h1></caption>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Descripcion</th>
                <th>Imagen</th>
                <?php if(isset($_SESSION["rol"])){
                    echo "<th>Añadir a Cesta</th>";
                }?>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($productos as $prod){
                    echo "<tr>";
                    echo "<td>" . $prod -> id_producto . "</td>";
                    echo "<td>" . $prod -> nombreProducto . "</td>";
                    echo "<td>" . $prod -> precio . "€</td>";
                    echo "<td>" . $prod -> cantidad . "</td>";
                    echo "<td>" . $prod -> descripcion . "</td>";
                    ?>
                    <td><img height="70" src="<?php echo $prod -> imagen ?>" alt=""></td>
                    <?php
                    if(isset($_SESSION["rol"])){
                        ?>
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
                                <input class="btn btn-primary" type="submit" value="Añadir a Cesta">
                                <?php if(isset($err_unidad)) echo $err_unidad ?>
                            </form>
                        </td>
                        <?php
                    }
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <?php
        if(isset($unidad)){
            echo "<h2>cantidad $unidad</h2>";
            echo "<h2>producto $idProducto</h2>";
            echo "<h2>cesta $cesta</h2>";

            $compruebaExiste = "SELECT cantidad FROM productosCestas 
                WHERE idProducto = '$idProducto' AND idCesta = '$cesta'";
            $resultadoExiste = $conexion->query($compruebaExiste);

            if ($resultadoExiste->num_rows > 0){
                $filaExiste = $resultadoExiste->fetch_assoc();
                $unidadesAntiguas = $filaExiste["cantidad"];
                $unidadesNuevas = $unidadesAntiguas + $unidad;
                $sql = "UPDATE productosCestas SET cantidad = '$unidadesNuevas' 
                    WHERE idProducto = '$idProducto' AND idCesta = '$cesta'";
            } else{
                $sql = "INSERT INTO productosCestas (idProducto, idCesta, cantidad)
                    VALUES ('$idProducto', '$cesta', '$unidad')";
            }

            $conexion -> query($sql);
        } 

    ?>
    <!-- Si es invitado que sea inicio de sesion el boton pero que haga cerrar sesion -->
    <a href="Funciones/cerrarSesion.php">Cerrar Sesion</a>

    <!-- Ir a cesta solo si rol admin o cliente -->
    <a href="cesta.php">Ir a cesta</a>

    <!-- Ir a cesta solo si rol admin -->
    <a href="productos.php">Añadir Producto</a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>