<?php

require '../vendor/autoload.php';
use \SendGrid\Mail\Mail;
global $conexion;

if (!empty($_POST["recuperarcontrasenabtn"])) {
    if (!empty($_POST["email"])) {
        $correorec = $conexion->real_escape_string($_POST["email"]);
        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $sql = $conexion->query("SELECT * FROM Usuarios WHERE correo='$correorec'");
        }else {
            echo "<div class= 'alert alert-danger'>Porfavor Ingrese un Correo Electrónico Valido!</div>";
        }
        if ($datos = $sql->fetch_object()) {
            echo "<div class= 'alert alert-success'>Hemos enviado un Codigo para Restablecer la Contraseña a tu Correo!</div>";
            try {
                $code = $conexion->real_escape_string(bin2hex(random_bytes(5)));
            } catch (Exception $e) {
            }
            $insert_code = "UPDATE Usuarios SET codigo = '$code' WHERE correo='$correorec'";
            $run_query = mysqli_query($conexion, $insert_code);
            if ($run_query) {
                $ConfigFile = parse_ini_file(realpath("../Configuracion/Config.ini"), true);
                $sendgrid = new SendGrid($ConfigFile['SendGrid']['apikey']);
                $email = new Mail();
                $email->addTo("{$correorec}", "Usuario");
                $email->setFrom("farmaciasemg@gmail.com", "Farmacia HG");
                $email->setSubject("Restablecimeinto de Conatraseña");
                $email->AddContent("text/html","<strong>Utilice el siguiente Codigo para Restablecer su Cuenta: {$code}</strong>");
                $sendgrid->send($email);
                header( "refresh:5;url=../Pantallas/Verificacion_Codigo.php" );
            } else {
                echo "<div class= 'alert alert-danger'>No se ha podido generar un Codigo para Restablecer la Contraseña de tu cuenta!</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Este Correo no esta Registrado!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Por Favor Ingrese un Correo Electrónico Valido!</div>";
    }
}