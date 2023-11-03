<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductoListado</title>
    <?php require 'Funciones/db_tiendas.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <?php
        $sql = "SELECT * FROM productos";
        $resultado = $conexion -> query($sql);
        $productos = [];

        while($prod = $resultado -> fetch_assoc()){
            $id_producto = $prod["idProducto"];
            $nombre = $prod["nombreProducto"];
            $precio = $prod["precio"];
            $descripcion = $prod["descripcion"];
            $cantidad = $prod["cantidad"];

            $nuevo_producto = [$id_producto, $nombre, $precio, $descripcion, $cantidad];
            array_push($productos, $nuevo_producto);
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
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($productos as $prod){
                    echo "<tr>";
                    echo "<td>" . $prod[0] . "</td>";
                    echo "<td>" . $prod[1] . "</td>";
                    echo "<td>" . $prod[2] . "</td>";
                    echo "<td>" . $prod[4] . "</td>";
                    echo "<td>" . $prod[3] . "</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>