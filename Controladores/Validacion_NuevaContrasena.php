<?php

global $conexion;

if (!empty($_POST["cambiocontrasenabtn"])) {
    if (!empty($_POST["newpassword1"]) and (!empty($_POST["newpassword2"]))) {

        $code = $conexion->real_escape_string(0);
        $codigo = $_SESSION['codigo'];

        $password1 = $conexion->real_escape_string($_POST['newpassword1']);
        $password2 = $conexion->real_escape_string($_POST['newpassword2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

        if (strlen($password1) >= 8) {
            if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                if ($password1 !== $password2) {
                    echo "<div class= 'alert alert-danger'>Las contraseñas ingresadas no coinciden!</div>";
                } else {
                    $update_password = "UPDATE Usuarios SET codigo = $code, contrasena = '$crypt_password' WHERE codigo = '$codigo'";
                    $run_query = mysqli_query($conexion, $update_password);
                    if ($run_query) {
                        echo "<div class= 'alert alert-success'>Su Contraseña se a cambiado Exitosamente!</div>";
                        session_start();
                        session_destroy();
                        header("refresh:3;url=../index.php");
                    } else {
                        echo "<div class= 'alert alert-danger'>Hemos tenido un problema al cambiar su contraseña, Por favor intente mas tarde!</div>";
                    }
                }
            }else {
                echo "<div class= 'alert alert-danger'>La Contraseña contiene Caracteres Especiales o Espacios que No estan Permitidos!</div>";
            }
        } else{
            echo "<div class= 'alert alert-danger'>La Contraseña debe tener al menos 8 Caracteres!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Por Favor Ingrese una Contraseña Valida en ambos campos!</div>";
    }
}

if (!empty($_POST["cambiocontrasenausuariobtn"])) {
    if ((!empty($_POST["currentpassword"])) and (!empty($_POST["newpassword1"])) and (!empty($_POST["newpassword2"]))) {
        $id_connected = $_POST['id_user'];
        $currentpassword = $conexion->real_escape_string($_POST["currentpassword"]);
        $password1 = $conexion->real_escape_string($_POST['newpassword1']);
        $password2 = $conexion->real_escape_string($_POST['newpassword2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

        $check_password = $conexion->query("SELECT * FROM Usuarios WHERE id='$id_connected'");
        $fetch = mysqli_fetch_assoc($check_password);
        $fetch_password = $fetch['contrasena'];

        if(password_verify($currentpassword, $fetch_password)){
            $check_password2 = $conexion->query("SELECT * FROM Usuarios WHERE id='$id_connected'");
            if ($datos=$check_password2->fetch_object()) {
                if (strlen($password1) >= 8) {
                    if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                        if ($password1 !== $password2) {
                            echo "<div class= 'alert alert-danger'>Las contraseñas ingresadas no coinciden!</div>";
                        } else {
                            $update_password = "UPDATE Usuarios SET contrasena = '$crypt_password' WHERE id = '$id_connected'";
                            $run_query = mysqli_query($conexion, $update_password);
                            if ($run_query) {
                                echo "<div class= 'alert alert-success'>Su Contraseña se a cambiado Exitosamente!</div>";
                                header("refresh:3;url=../Pantallas/Inicio.php");
                            } else {
                                echo "<div class= 'alert alert-danger'>Hemos tenido un problema al cambiar su contraseña, Por favor intente mas tarde!</div>";
                            }
                        }
                    }else {
                        echo "<div class= 'alert alert-danger'>La Contraseña contiene Caracteres Especiales o Espacios que No estan Permitidos!</div>";
                    }
                } else{
                    echo "<div class= 'alert alert-danger'>La Contraseña debe tener al menos 8 Caracteres!</div>";
                }
            }
        }else{
            echo "<div class= 'alert alert-danger'>La Contraseña Actual es Incorrecta!</div>";
        }

    }else{
        echo "<div class= 'alert alert-danger'>Por Favor rellene todos los campos!</div>";
    }
}

