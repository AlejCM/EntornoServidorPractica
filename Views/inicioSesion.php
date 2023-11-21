<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Sesion</title>
    <?php require '../Util/db_tiendas.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="Styles/styles.css">
</head>
<body>
    <div class="container">
        <h1>Inicio de Sesion</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="contrasena">
            </div>
            <input class="btn btn-primary" type="submit" value="Registrarse">
        </form>
        <div class="botones">
            <!-- Deja crear usuario -->
            <a class="btn btn-primary" href="usuario.php">Crear Usuario</a><br>
            <!-- Deja entrar como invitado -->
            <a class="btn btn-primary" href="productosListado.php">Entrar como invitado</a>
        </div>
        

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $usuario = $_POST["usuario"];
            $contrasena = $_POST["contrasena"];

            $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
            $resultado = $conexion -> query($sql);
            
            if ($resultado -> num_rows === 0){
                echo "<h2>El usuario no existe</h2>";
            }else{
                while ($fila = $resultado -> fetch_assoc()){
                    $contrasena_cifrada = $fila["contrasena"];
                }
        
                $acceso_valido = password_verify($contrasena, $contrasena_cifrada);
        
                if ($acceso_valido) {
                    session_start();
                    $_SESSION["usuario"] = $usuario;
                    /* Guardo el rol del usuario */
                    $consulta = "SELECT rol FROM usuarios WHERE usuario='$usuario'";
                    $resultado = $conexion->query($consulta);
                    $fila = $resultado->fetch_assoc();
                    $rol = $fila["rol"];
                    $_SESSION["rol"] = $rol;
                    /* Guardo el id de la cesta del usuario */
                    $consultaCesta = "SELECT idCesta FROM cestas WHERE usuario='$usuario'";
                    $resultadoCesta = $conexion->query($consultaCesta);
                    $filaCesta = $resultadoCesta->fetch_assoc();
                    $cesta = $filaCesta["idCesta"];
                    $_SESSION["idCesta"] = $cesta;
                    
                    header('location: productosListado.php');
        
                }else{
                    echo "<h2>El usuario o la Contraseña es Incorrecta</h2>";
                }
            }
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>