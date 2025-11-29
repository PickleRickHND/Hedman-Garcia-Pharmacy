<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

// Validate that user can only access their own settings
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($_SESSION["id"] != $id) {
    header("Location: error_page.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;

// Use Prepared Statement
$stmt = $connection->prepare("SELECT id, nombre, apellido, correo, roles FROM Usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$selectUser = $stmt->get_result();

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
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css" type="text/css">

    <style>
        body {
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>

</head>

<body style="margin-top: 5%">
    <div class="container w-75 bg-white mt-5 rounded shadow">
        <div class="row align-items-center">

            <div class="fw-bold text-center d-grid">
                <button id="goBackButton" type="button" class="btn btn-small btn-secondary btn-block" style="margin-top: 20px; width: fit-content;" onclick="goBack()"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg> Go Back
                </button>

                <script>
                    function goBack() {
                        window.history.back();
                    }
                </script>

                <style>
                    @media (max-width: 768px) {
                        #goBackButton {
                            display: none;
                        }
                    }
                </style>
            </div>

            <img src="../images/settingsImage.png" style="width:560px; display: block; margin-left: auto; margin-right: auto;" alt="">
            <div class="col bg-white p-5 rounded bg">
                <h2 class="fw-bold text-center py-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
                <h4 class="fw-bold text-center py-5">Account Settings</h4>
                <form method="post" action="">
                    <?php
                    include "../controllers/validations.php";

                    while ($data = $selectUser->fetch_object()) { ?>


                        <div class="mb-3">
                            <input type="hidden" name="id_user" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
                            <label for="recipient-name1" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="recipient-name1" placeholder="Nombre" name="name" value="<?= htmlspecialchars($data->nombre, ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="recipient-name2" class="col-form-label">Last Name:</label>
                            <input type="text" class="form-control" id="recipient-name2" placeholder="Apellido" name="lastname" value="<?= htmlspecialchars($data->apellido, ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="recipient-name3" class="col-form-label">Email:</label>
                            <input type="text" class="form-control" id="recipient-name3" placeholder="Correo Electrónico" name="email" value="<?= htmlspecialchars($data->correo, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    <?php }
                    $stmt->close();
                    ?>
                    <div class="fw-bold text-center">
                        <br>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a type="button" class="btn btn-danger me-md-2 mb-2" href="home.php">Close</a>
                            <input type="submit" class="btn btn-primary me-md-2 mb-2" value="Edit" name="edit_user_settings_button">
                            <a type="button" class="btn btn-warning mb-2" href="change_password.php?id=<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">Change Password</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
</body>

</html>
