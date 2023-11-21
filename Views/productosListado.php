<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductoListado</title>
    <?php require '../Util/db_tiendas.php' ?>
    <?php require '../Util//Producto.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="Styles/styles.css">
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
            $usuario = $_SESSION["usuario"];
        }

        /* Añade los productos a un array de objetos Producto */
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

        /* Comprueba las unidades del producto cuando se envia el formulario*/
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $idProducto = $_POST["idProducto"];
            $unidad_temp = $_POST["unidad"];
            $unidadesPermitidas = ['1', '2', '3', '4', '5'];

            if (!isset($unidad_temp) || !in_array($unidad_temp, $unidadesPermitidas)){
                $err_unidad = "No hay nada gratis por aqui";
            } else{
                $compruebaCantidad = "SELECT cantidad FROM productos 
                    WHERE idProducto = '$idProducto'";
                $cantidadDisponible = $conexion->query($compruebaCantidad);
                $fila = $cantidadDisponible->fetch_assoc();
                $cantidadUnidades = $fila["cantidad"];

                if ($cantidadUnidades < $unidad_temp){
                    $err_unidad = "No quedan tantas unidades disponibles";
                }else{
                    $unidad = $unidad_temp;
                }
            }
        }
    ?>
    <div class="nav">
        <div class="titulo">
            <h1>Bienvenido <?php echo $usuario ?></h1>
        </div>
        <div class="enlaces">
            <?php
                if ($_SESSION["usuario"] == "invitado"){
                    ?>
                    <!-- Si es invitado pone Inicio de Sesion -->
                    <a class="btn btn-primary" href="../Util/cerrarSesion.php">Iniciar Sesion</a>
                    <?php
                } else{
                    if ($_SESSION["rol"] == "admin"){
                        ?>
                        <!-- Añadir producto solo si el rol es admin -->
                        <a class="btn btn-primary" href="productos.php">Añadir Producto</a>
                        <?php
                    }
                    ?>
                    <!-- Ir a cesta y cerrar sesion para todos los usuarios -->
                    <a class="btn btn-primary" href="cesta.php">Ir a cesta</a>
                    <a class="btn btn-primary" href="../Util/cerrarSesion.php">Cerrar Sesion</a>
                    <?php
                }
            ?>
        </div>
    </div>

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
                    <td><img height="70" src="<?php echo $prod -> imagen ?>" alt="Imagen"></td>
                    <?php
                    //Si tiene cualquier rol puede añadir a cesta
                    if(isset($_SESSION["rol"])){
                        ?>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="idProducto" value="<?php echo ($prod -> id_producto) ?>">
                                <select id="unidad" name="unidad">
                                    <option value="1" selected>1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <!-- Cambia a Sin Stock cuando no quedan unidades -->
                                <input class="btn btn-primary" type="submit" value=
                                <?php if ($prod -> cantidad == 0){
                                    echo "'Sin Stock' disabled";
                                } else{
                                    echo "'Añadir a Cesta'";
                                } ?>>
                            </form>
                        </td>
                        <?php
                    }
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <?php if(isset($err_unidad)) echo $err_unidad ?>
    <br>
    <?php
        if(isset($unidad)){
            /* Añade productos a productosCestas con la cantidad indicada */
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


            /* Cambia el precio total */
            $compruebaPrecio = "SELECT precio FROM productos
                WHERE idProducto = '$idProducto'";
            $resultadoPrecio = $conexion->query($compruebaPrecio);
            $filaPrecio = $resultadoPrecio->fetch_assoc();
            $precioUnidad = $filaPrecio["precio"];
            $precioExtra = $unidad * $precioUnidad;
            $sql = "UPDATE cestas SET precioTotal = precioTotal + '$precioExtra' 
                WHERE idCesta = '$cesta'";
            $conexion -> query($sql);

            /* Cambia la cantidad de producto en la lista restando lo que se ha añadido a la cesta
            Hemos comprobado antes que no pueda ser negativo haciendo que no se pueda
            coger una cantidad de unidades a restar mayor a la cantidad total */
            $unidadesNuevas = $cantidadUnidades - $unidad;
            $sql = "UPDATE productos SET cantidad = '$unidadesNuevas' 
                WHERE idProducto = '$idProducto'";
            $conexion -> query($sql);
            ?>
            <!-- Recarga la pagina para evitar problemas con header, lo hago con un script -->
            <script>window.location.href = "productosListado.php";</script>
            <?php
        } 
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>