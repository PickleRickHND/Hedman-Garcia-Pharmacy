<?php
session_start();
if(empty($_SESSION["id"])){
    header("Location: ../index.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;

$id=$_GET['id'];
$selectUser=$connection->query("SELECT * FROM Usuarios WHERE id='$id'");

?>

<!DOCTYPE html>

<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <title>Edit User</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">


    <style>
        body{
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>

</head>
<body>
<div class="container w-75 bg-white mt-5 rounded shadow">
    <div class="row align-items-center align-items-stretch">
        <div class="col bg-white p-5 rounded bg">
            <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
            <h3 class="fw-bold text-center ру-5">Edit User </h3>
            <br>
            <div>
                <form method="post" action="">
                    <?php
                    include "../controllers/validations.php";
                    while ($data=$selectUser->fetch_object()){ ?>

                    <div class="mb-3">
                        <input type="hidden" name="id_user" value="<?= $_GET['id']?>">
                        <label for="recipient-name1" class="col-form-label">Name: </label>
                        <input type="text" class="form-control" id="recipient-name1" placeholder="Nombre" name="name" value="<?= $data->nombre?>">
                        <br>
                        <label for="recipient-name2" class="col-form-label">Last Name: </label>
                        <input type="text" class="form-control" id="recipient-name2" placeholder="Apellido" name="lastname" value="<?= $data->apellido?>">
                        <br>
                        <label for="recipient-name3" class="col-form-label">Email: </label>
                        <input type="text" class="form-control" id="recipient-name3" placeholder="Correo Electrónico" name="email" value="<?= $data->correo?>">
                        <br>
                        <label for="recipient-name3" class="col-form-label">Roles: </label>
                        <select class="form-select" name="roles" value="<?= $data->roles?>">
                            <?php
                            $query_roles = $connection->query("SELECT nombre_rol FROM Roles");
                            while($fetch_Roles = $query_roles->fetch_object()) {
                                echo '<option value="' . $fetch_Roles->nombre_rol . '">' . $fetch_Roles->nombre_rol . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php }
                    ?>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-danger" href="user_management.php">Close</a>
                        <input type="submit" class="btn btn-primary" value="Edit" name="edit_user_button">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script src="../controllers/functions.js"></script>