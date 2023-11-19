<?php
session_start();
if(empty($_SESSION["id"])){
    header("Location: ../index.php");
    exit;
}

global $connection;
?>

<!DOCTYPE html>

<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icono.png">
    <title>User Management</title>

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
<body>
<div class="container w-75 bg-white mt-5 rounded shadow">
    <div class="row align-items-center align-items-stretch">
        <div class="col bg-white p-5 rounded bg">
            <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
            <h3 class="fw-bold text-center ру-5"><strong>User Management</strong></h3>
            <br>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-small btn-primary" data-toggle="modal" data-target="#NuevoUsuarioModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill-add" viewBox="0 0 20 20">
                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Zm-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        <path d="M2 13c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Z"/>
                    </svg> Add New User
                </button>
                <input type="text" style="width: 300px" class="form-control" id="buscar-usuario" placeholder="Search Users">

                <script>
                    $(document).ready(function() {
                        $("#buscar-usuario").on("keyup", function() {
                            var searchText = $(this).val().toLowerCase();
                            $("tbody tr").filter(function() {
                                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1)
                            });
                        });
                    });
                </script>
            </div>

            <?php include "../settings/db_connection.php";?>
            <?php include "../controllers/validations.php";?>
            <?php include "../controllers/delete_user.php";?>
            <?php include "../controllers/force_reset_password.php";?>

            <!-- Modal -->
            <div class="modal fade" id="NuevoUsuarioModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add New User:</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" >
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="recipient-name1" class="col-form-label"></label>
                                    <input type="text" class="form-control" id="recipient-name1" placeholder="Name" name="name">

                                    <label for="recipient-name2" class="col-form-label"></label>
                                    <input type="text" class="form-control" id="recipient-name2" placeholder="Last Name" name="lastname">

                                    <label for="recipient-name3" class="col-form-label"></label>
                                    <input type="email" class="form-control" id="recipient-name3" placeholder="Email" name="email">

                                    <br>

                                    <select class="form-select js-example-basic-multiple" name="roles">
                                        <option>Roles</option>
                                        <?php
                                        $query_roles = $connection->query("SELECT nombre_rol FROM Roles");
                                        while($fetch_Roles = $query_roles->fetch_object()) {
                                            echo '<option value="' . $fetch_Roles->nombre_rol . '">' . $fetch_Roles->nombre_rol . '</option>';
                                        }
                                        ?>
                                    </select>



                                    <label for="recipient-name4" class="col-form-label"></label>
                                    <input type="password" class="form-control" id="recipient-name4" placeholder="Password" name="password1">

                                    <label for="recipient-name5" class="col-form-label"></label>
                                    <input type="password" class="form-control" id="recipient-name5" placeholder="Verify Password" name="password2">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Save" name="save_user_button">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="text-align:center" scope="col">Name</th>
                    <th style="text-align:center" scope="col">Last Name</th>
                    <th style="text-align:center" scope="col">Email</th>
                    <th style="text-align:center" scope="col">Roles</th>
                    <th style="text-align:center" scope="col">Actions</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                include "../settings/db_connection.php";
                global $connection;
                $sql=$connection->query("SELECT * FROM Usuarios");
                while($datos=$sql->fetch_object()){ ?>
                    <tr>
                        <td style="text-align:center"><?= $datos->nombre?></td>
                        <td style="text-align:center"><?= $datos->apellido?></td>
                        <td style="text-align:center"><?= $datos->correo?></td>
                        <td style="text-align:center"><?= $datos->roles?></td>
                        <td style="text-align:center">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="edit_user.php?id=<?= $datos->id?>" class="btn btn-small btn-warning"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 20 20">
                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                    </svg> Edit</a>
                                <div class="mx-1"></div>
                                <a onclick="return eliminar()" href="user_management.php?id=<?= $datos->id?>" class="btn btn-small btn-danger"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 20 20">
                                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                    </svg> Delete</a>
                                <div class="mx-1"></div>
                                <a onclick="return restablecerPassword()" href="../controllers/force_reset_password.php?id=<?= $datos->id?>" class="btn btn-small btn-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 20 20">
                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg> Reset Password</a>
                            </div>
                        </td>

                        <script>
                            function eliminar(){
                                var confirmacion=confirm("Are you sure to Delete this user?")
                                return confirmacion;
                            }
                        </script>

                        <script>
                            function restablecerPassword(){
                                var confirmacion2=confirm("Are you sure you want to Reset the Password for this user? The New Password will be: P@55W0RD")
                                return confirmacion2;
                            }
                        </script>
                    </tr>
                <?php }
                ?>
                </tbody>
            </table>
            <br>
        </div>
    </div>
</div>
</body>
</html>

