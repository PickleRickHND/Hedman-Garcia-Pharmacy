<?php

global $conexion;
global $correorec;
global $code;

require '../vendor/autoload.php';
use \SendGrid\Mail\Mail;
session_start();

if (!empty($_POST["validarcodigoabtn"])) {
    if (!empty($_POST["code"])) {
        $codigo = $conexion->real_escape_string($_POST["code"]);
        $sql = $conexion->query("SELECT * FROM Usuarios WHERE codigo='$codigo'");
        if ($datos = $sql->fetch_object()) {
            $_SESSION["codigo"]=$datos->codigo;
            echo "<div class= 'alert alert-success'>El codigo ingresado es correcto, Procedamos a cambiar tu Contraseña!</div>";
            header( "refresh:3;url=../Pantallas/Nueva_Contrasena.php" );

        } else {
            echo "<div class= 'alert alert-danger'>El codigo ingresado es Incorrecto!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Por Favor Ingrese el Codigo de Verificación!</div>";
    }
}

if (!empty($_POST["reenviarcodigoabtn"])) {
    if (empty($_POST["code"])) {
        echo "<div class= 'alert alert-success'>Hemos Reenviado el Codigo de Restablecimiento a tu Correo!</div>";
        $ConfigFile = parse_ini_file(realpath("../Configuracion/Config.ini"), true);
        $sendgrid = new SendGrid($ConfigFile['SendGrid']['apikey']);
        $email = new Mail();
        $email->addTo("{$correorec}", "Usuario");
        $email->setFrom("farmaciasemg@gmail.com", "Farmacia HG");
        $email->setSubject("Restablecimeinto de Conatraseña");
        $email->AddContent("text/html","<strong>Utilice el siguiente Codigo para Restablecer su Cuenta: {$code}</strong>");
        $sendgrid->send($email);
    }
}