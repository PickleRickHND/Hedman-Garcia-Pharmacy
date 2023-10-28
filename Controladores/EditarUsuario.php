<?php

global $conexion;

if (!empty($_POST["editarusuariobtn"])){
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["roles"]))){
        $id=$_POST['id_user'];
        $name = $conexion->real_escape_string($_POST['name']);
        $lastname = $conexion->real_escape_string($_POST['lastname']);
        $email = $conexion->real_escape_string($_POST['email']);
        $roles = $conexion->real_escape_string($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)){
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $edit=$conexion->query("UPDATE Usuarios SET nombre='$name',apellido='$lastname',correo='$email',roles='$roles' WHERE id='$id'");

                    if ($edit == 1){
                        echo "<div class= 'alert alert-success'>Se ha Modificado el Usuario Correctamente!</div>";
                        header( "refresh:3;url=Administracion_Usuarios.php" );
                    }else{
                        echo "<div class= 'alert alert-danger'>Hemos tenido problemas al modificar este Usuario!</div>";
                    }

                }else{
                    echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Correo Electrónico Valido!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Apellido Válido!</div>";
            }

        }else{
            echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Nombre Válido!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Porfavor Rellene Todos los Campos e Intentelo Nuevamente!</div>";
    }
}

if (!empty($_POST["editarusuarioconfiguracionbtn"])){
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"]))){
        $id=$_POST['id_user'];
        $name = $conexion->real_escape_string($_POST['name']);
        $lastname = $conexion->real_escape_string($_POST['lastname']);
        $email = $conexion->real_escape_string($_POST['email']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)){
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $edit=$conexion->query("UPDATE Usuarios SET nombre='$name',apellido='$lastname',correo='$email' WHERE id='$id'");

                    if ($edit == 1){
                        echo "<div class= 'alert alert-success'>Se ha Modificado el Usuario Correctamente!</div>";
                        header( "refresh:3;url=Inicio.php" );
                    }else{
                        echo "<div class= 'alert alert-danger'>Hemos tenido problemas al modificar este Usuario!</div>";
                    }

                }else{
                    echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Correo Electrónico Valido!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Apellido Válido!</div>";
            }

        }else{
            echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Nombre Válido!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Porfavor Rellene Todos los Campos e Intentelo Nuevamente!</div>";
    }
}
