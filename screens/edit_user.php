<?php
require_once "../settings/session_config.php";
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

// Check RBAC - only Administrador can edit users
$user_role = isset($_SESSION["roles"]) ? $_SESSION["roles"] : '';
if ($user_role !== 'Administrador') {
    header("Location: error_page.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt_user = $connection->prepare("SELECT * FROM Usuarios WHERE id = ?");
$stmt_user->bind_param("i", $id);
$stmt_user->execute();
$selectUser = $stmt_user->get_result();

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <title>Edit User</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    <style>
        body {
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>

</head>

<body>
    <div class="container w-75 bg-white mt-5 rounded shadow">
        <div class="row align-items-center align-items-stretch">
            <div class="col bg-white p-5 rounded bg">
                <h2 class="fw-bold text-center py-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
                <h3 class="fw-bold text-center py-5">Edit User </h3>
                <br>
                <div>
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <?php
                        include "../controllers/validations.php";
                        while ($data = $selectUser->fetch_object()) { ?>

                            <div class="mb-3">
                                <input type="hidden" name="id_user" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
                                <label for="recipient-name1" class="col-form-label">Name: </label>
                                <input type="text" class="form-control" id="recipient-name1" placeholder="Nombre" name="name" value="<?= htmlspecialchars($data->nombre, ENT_QUOTES, 'UTF-8') ?>">
                                <br>
                                <label for="recipient-name2" class="col-form-label">Last Name: </label>
                                <input type="text" class="form-control" id="recipient-name2" placeholder="Apellido" name="lastname" value="<?= htmlspecialchars($data->apellido, ENT_QUOTES, 'UTF-8') ?>">
                                <br>
                                <label for="recipient-name3" class="col-form-label">Email: </label>
                                <input type="text" class="form-control" id="recipient-name3" placeholder="Correo Electrónico" name="email" value="<?= htmlspecialchars($data->correo, ENT_QUOTES, 'UTF-8') ?>">
                                <br>
                                <label for="recipient-name3" class="col-form-label">Roles: </label>
                                <select class="form-select" name="roles">
                                    <?php
                                    $query_roles = $connection->query("SELECT nombre_rol FROM Roles");
                                    while ($fetch_Roles = $query_roles->fetch_object()) {
                                        $selected = ($fetch_Roles->nombre_rol === $data->roles) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($fetch_Roles->nombre_rol, ENT_QUOTES, 'UTF-8') . '" ' . $selected . '>' . htmlspecialchars($fetch_Roles->nombre_rol, ENT_QUOTES, 'UTF-8') . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php }
                        $stmt_user->close();
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
