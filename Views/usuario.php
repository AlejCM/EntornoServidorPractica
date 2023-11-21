<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require 'Funciones/util.php' ?>
    <?php require 'Funciones/db_tiendas.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        a{
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $temp_usuario = depurar($_POST["usuario"]);
        $temp_contrasena = depurar($_POST["contrasena"]);
        $temp_nacimiento = depurar($_POST["fecha_nacimiento"]);
        if (strlen($temp_usuario) == 0){
            $err_usuario = "Campo Incompleto";
        } else{
            $regex = "/^([a-zA-Z0-9_]{4,12})$/"; 
            if (!preg_match($regex, $temp_usuario)){
                $err_usuario = "El usuario debe contener de 4-12 caracteres";
            } else{
                $usuario = $temp_usuario;
            }
        }

        if (strlen($temp_contrasena) == 0){
            $err_contrasena = "Campo Incompleto";
        } else{
            if (strlen($temp_contrasena) > 20){
                $err_contrasena = "La contraseña es muy larga";
            } else{
                if (strlen($temp_contrasena) < 8){
                    $err_contrasena = "La contraseña es muy corta";
                } else{
                    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,20}$/"; 
                    if (!preg_match($regex, $temp_contrasena)){
                        $err_contrasena = "La contraseña debe tener al menos una mayuscula, una minuscula, 
                        un numero y un caracter especial";
                    } else{
                        $contrasena = $temp_contrasena;
                    }
                }
            }
        }

        if (strlen($temp_nacimiento) == 0){
            $err_nacimiento = "Campo Incompleto";
        } else{
            $dt = new DateTime($temp_nacimiento);
            $fecha_actual = new DateTime();
            $edad = $fecha_actual -> diff($dt) -> y;
            if ($edad<12 || $edad>120){
                $err_nacimiento = "Muy joven/viejo";
            } else{
                $f_nacimiento = $temp_nacimiento;
            }
        }
    }

    /*
    AleCM -- Medac
    Manu -- 1234
    Julio -- 1234
    */

    ?>
    <div class="container">
    <h1>Nuevo Usuario</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario">
                <?php if(isset($err_usuario)) echo $err_usuario ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="contrasena">
                <?php if(isset($err_contrasena)) echo $err_contrasena ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de Nacimiento</label>
                <input class="form-control" type="date" name="fecha_nacimiento">
                <?php if(isset($err_nacimiento)) echo $err_nacimiento ?>
            </div>
            <input class="btn btn-primary" type="submit" value="Crear Usuario">
        </form>
        <?php 
        if(isset($usuario) && isset($contrasena) && isset($f_nacimiento)){

            $contrasena_cifrada = password_hash($contrasena, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento)
                VALUES ('$usuario', '$contrasena_cifrada', '$f_nacimiento')";
            $conexion -> query($sql);

            $sql = "INSERT INTO cestas (usuario, precioTotal)
                VALUES ('$usuario', 0)";
            $conexion -> query($sql);

            header('location: inicioSesion.php');
        } 
        ?>
        <a class="btn btn-primary" href="Funciones/cerrarSesion.php">Inicio de Sesion</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>