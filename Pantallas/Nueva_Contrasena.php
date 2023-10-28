<?php
session_start();
if(empty($_SESSION["codigo"])){
    header("Location: Recuperar_Contrasena.php");
    exit;
}
?>

<!DOCTYPE html>

<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Imagenes/icono.png">
    <title>Farmacia HG</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../CSS/styles.css" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <style>
        body{
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>

</head>
<body>
<div class="container w-75 bg-white mt-5 rounded shadow">
    <div class="row align-items-center align-items-stretch">
        <img src="../Imagenes/Farmaceutico%20en%20el%20mostrador%20con%20cliente.png" style="width:550px" alt="">
        <div class="col bg-white p-5 rounded bg">
            <h2 class="fw-bold text-center ру-5"><strong>Farmacia Hedman Garcia</strong></h2><br>
            <h4 class="fw-bold text-center ру-5">Cambio de Contraseña</h4>
            <br>
            <form method="post" action="">
                <div class="input-group mb-4">
                    <span class="input-group-text" id="password"><i class="bi bi-key-fill"></i></span>
                    <input name="newpassword1" id="newpassword1" class="form-control" type="password" placeholder="Nueva Contraseña">
                </div>

                <div class="input-group mb-4">
                    <span class="input-group-text" id="password"><i class="bi bi-key-fill"></i></span>
                    <input name="newpassword2" id="newpassword2" class="form-control" type="password" placeholder="Confirmar Nueva Contraseña">
                </div>

                <div class="d-grid">
                    <input type="submit" class="btn btn-primary btn-block" value="Confirmar" name="cambiocontrasenabtn">
                </div>
                <br>
                <?php include "../Configuracion/Conexion.php";?>
                <?php include "../Controladores/Validacion_NuevaContrasena.php";?>
            </form>
        </div>
    </div>
</div>
</body>
</html>