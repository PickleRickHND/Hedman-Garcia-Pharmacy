<?php
session_start();
if(empty($_SESSION["id"])){
    header("Location: ../index.php");
    exit;
}

if ($_SESSION["id"] != $_GET['id']) {
    header("Location: error_page.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;
$id=$_GET['id'];
$seleccionar=$connection->query("SELECT * FROM Usuarios WHERE id='$id'");
?>

<!DOCTYPE html>

<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icono.png">
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css" type="text/css">

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
<body style="margin-top: 5%">
<div class="container w-75 bg-white mt-5 rounded shadow">
    <div class="row align-items-center">
        <img src="../images/Mostradora%20con%20Cliente.png" style="width:560px" alt="">
        <div class="col bg-white p-5 rounded bg">
            <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
            <h4 class="fw-bold text-center ру-5">Account Settings</h4>
            <form method="post" action="">
                <?php
                include "../controllers/validations.php";

                $sql=$connection->query("SELECT * FROM Usuarios");
                $datos2=$sql->fetch_object();
                while ($datos=$seleccionar->fetch_object()){ ?>


                    <div class="mb-3">
                        <input type="hidden" name="id_user" value="<?= $_GET['id']?>">
                        <label for="recipient-name1" class="col-form-label">Name:</label>
                        <input type="text" class="form-control" id="recipient-name1" placeholder="Nombre" name="name" value="<?= $datos->nombre?>">
                    </div>

                    <div class="mb-3">
                        <label for="recipient-name2" class="col-form-label">Last Name:</label>
                        <input type="text" class="form-control" id="recipient-name2" placeholder="Apellido" name="lastname" value="<?= $datos->apellido?>">
                    </div>

                    <div class="mb-3">
                        <label for="recipient-name3" class="col-form-label">Email:</label>
                        <input type="text" class="form-control" id="recipient-name3" placeholder="Correo Electrónico" name="email" value="<?= $datos->correo?>">
                    </div>
                <?php }
                ?>
                <div class="fw-bold text-center">
                    <br>
                    <a type="button" class="btn btn-danger" href="home.php">Close</a>
                    <input type="submit" class="btn btn-primary" value="Edit" name="edit_user_settings_button">
                    <a type="button" class="btn btn-warning" href="change_password.php?id=<?= $datos2->id?>">Change Password</a>
                </div>
            </form>

    </div>
</div>
</body>
</html>

