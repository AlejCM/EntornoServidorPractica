<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <?php require 'Funciones/util.php' ?>
    <?php require 'Funciones/db_tiendas.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $temp_nombre = depurar($_POST["nombre"]);
        $temp_precio = depurar($_POST["precio"]);
        $temp_descripcion = depurar($_POST["descripcion"]);
        $temp_cantidad = depurar($_POST["cantidad"]);
        
        if (strlen($temp_nombre) == 0){
            $err_nombre = "Campo Incompleto";
        } else{
            $regex = "/^([a-zA-Z0-9_ ]{1,40})$/"; 
            if (!preg_match($regex, $temp_nombre)){
                $err_nombre = "El nombre tiene que ser menos de 40 caracteres";
            } else{
                $nombre = $temp_nombre;
            }
        }

        if (strlen($temp_precio) == 0){
            $err_precio = "Campo Incompleto";
        } else{
            if ($temp_precio < 0 || $temp_precio > 99999.99){
                $err_precio = "El precio debe ser entre 0 y 99999.99";
            } else{
                $precio = $temp_precio;
            }
        }

        if (strlen($temp_descripcion) == 0){
            $err_descripcion = "Campo Incompleto";
        } else{
            if (strlen($temp_descripcion) > 255){
                $err_descripcion = "La descripcion no puede tener mas de 255 caracteres";
            } else{
                $descripcion = $temp_descripcion;
            }
        }
        
        if (strlen($temp_cantidad) == 0){
            $err_cantidad = "Campo Incompleto";
        } else{
            $regex = "/^([0-9]{0,5})$/"; 
            if (!preg_match($regex, $temp_cantidad)){
                $err_cantidad = "La cantidad debe ser entre 0 y 99999";
            } else{
                $cantidad = $temp_cantidad;
            }
        }
    }
    ?>
    <div class="container">
        <h1>Nuevo Producto</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input class="form-control" type="text" name="nombre">
                <?php if(isset($err_nombre)) echo $err_nombre ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input class="form-control" type="text" name="precio">
                <?php if(isset($err_precio)) echo $err_precio ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <input class="form-control" type="text" name="descripcion">
                <?php if(isset($err_descripcion)) echo $err_descripcion ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input class="form-control" type="text" name="cantidad">
                <?php if(isset($err_cantidad)) echo $err_cantidad ?>
            </div>
            <input class="btn btn-primary" type="submit" value="Enviar">
        </form>

        <?php 
            if(isset($nombre) && isset($precio) && isset($descripcion) && isset($cantidad)){
            echo "<h2>$nombre</h2>";
            echo "<h2>$precio</h2> ";
            echo "<h2>$cantidad</h2>";
            echo "<p>$descripcion</p>";
            

            $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad)
                VALUES ('$nombre', '$precio', '$descripcion', '$cantidad')";
            
            $conexion -> query($sql);
        } 
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>