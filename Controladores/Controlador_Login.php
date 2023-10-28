<?php

session_start();
global $conexion;
if (!empty($_POST["iniciarsesionbtn"])){
    if(!empty($_POST["email"]) and (!empty($_POST["password"]))){

        $correo = $conexion->real_escape_string($_POST["email"]);
        $contrasena = $conexion->real_escape_string($_POST["password"]);
        $check_correo = $conexion->query("SELECT * FROM Usuarios WHERE correo='$correo'");

        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        }else {
            echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Correo Electrónico Valido!</div>";
        }
        if(mysqli_num_rows($check_correo) > 0){
            $fetch = mysqli_fetch_assoc($check_correo);
            $fetch_password = $fetch['contrasena'];
            if(password_verify($contrasena, $fetch_password)){
                $check_correo2 = $conexion->query("SELECT * FROM Usuarios WHERE correo='$correo'");
                if ($datos=$check_correo2->fetch_object()) {
                    $_SESSION["id"] = $datos->id;
                    $_SESSION["nombre"] = $datos->nombre;
                    $_SESSION["apellido"] = $datos->apellido;
                    header("location: Pantallas/Inicio.php");
                }
            }else{
                echo "<div class= 'alert alert-danger'>Contraseña Incorrecta!</div>";
            }

        }else{
            echo "<div class= 'alert alert-danger'>El Correo Ingresado No se encuentra Registrado!</div>";
        }

    }else{
        echo "<div class= 'alert alert-danger'>Por Favor rellene todos los campos para poder continuar!</div>";
    }
}

