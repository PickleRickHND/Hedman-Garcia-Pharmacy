<?php

include "../Configuracion/Conexion.php";
global $conexion;

if (!empty($_POST["guardarusuariobtn"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["password1"])) and (!empty($_POST["password2"])) and (!empty($_POST["roles"]))){
        $name = $conexion->real_escape_string($_POST['name']);
        $lastname = $conexion->real_escape_string($_POST['lastname']);
        $email = $conexion->real_escape_string($_POST['email']);
        $password1 = $conexion->real_escape_string($_POST['password1']);
        $password2 = $conexion->real_escape_string($_POST['password2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);
        $roles = $conexion->real_escape_string($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)){
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)){
                if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    if (strlen($password1) >= 8) {
                        if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                            if ($password1 !== $password2) {
                                echo "<div class= 'alert alert-danger'>Las contraseñas ingresadas no coinciden!</div>";
                            } else {
                                $check_correo = $conexion->query("SELECT * FROM Usuarios WHERE correo='$email'");
                                if (mysqli_num_rows($check_correo) > 0){
                                    echo "<div class= 'alert alert-danger'>El Correo Utilizado ya se encuetra Registrado!</div>";
                                }else{
                                    function generarID($conexion) {
                                        $numeroID = rand(1000, 999999);
                                        $query = "SELECT * FROM Usuarios WHERE id = $numeroID";
                                        $result = $conexion->query($query);

                                        if ($result->num_rows > 0) {
                                            return generarID($conexion);
                                        } else {
                                            return $numeroID;
                                        }
                                    }
                                    $numeroID = generarID($conexion);
                                    $insert_user = "INSERT INTO Usuarios (id,nombre,apellido,correo,contrasena,roles) VALUES ('$numeroID','$name','$lastname','$email','$crypt_password','$roles')";
                                    $response = mysqli_query($conexion, $insert_user);

                                    if ($response === TRUE) {
                                        echo "<div class= 'alert alert-success'>Se ha Agregado un Nuevo Usuario Correctamente!</div>";
                                    } else {
                                        echo "<div class= 'alert alert-danger'>Se ha generado un Error al Agregar el Usuario!</div>";
                                    }
                                }
                            }
                        } else {
                            echo "<div class= 'alert alert-danger'>La Contraseña contiene Caracteres Especiales o Espacios que No estan Permitidos!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>La Contraseña debe tener al menos 8 Caracteres!</div>";
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

