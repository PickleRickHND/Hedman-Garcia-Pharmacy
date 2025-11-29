<?php
require_once "../settings/session_config.php";
if (empty($_SESSION["id"])) {
    header("Location: home.php");
    exit;
}

// Validate and sanitize the ID parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SESSION["id"] != $id) {
    header("Location: error_page.php");
    exit;
}

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
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <style>
        body {
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>

</head>

<body style="margin-top: 8%">
    <div class="container w-75 bg-white mt-5 rounded shadow">
        <div class="row align-items-center align-items-stretch">

            <div class="fw-bold text-center d-grid">
                <button type="button" class="btn btn-small btn-secondary btn-block" style="margin-top: 20px; width: fit-content;" onclick="goBack()"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg> Go Back
                </button>

                <script>
                    function goBack() {
                        window.history.back();
                    }
                </script>
            </div>

            <img src="../images/loginImage.png" style="width:560px" alt="">
            <div class="col bg-white p-5 rounded bg">
                <h2 class="fw-bold text-center py-5">Hedman Garcia Pharmacy</h2><br>
                <h4 class="fw-bold text-center py-5">Change Password</h4>
                <br>
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="id_user" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="input-group mb-4">
                        <span class="input-group-text" id="password"><i class="bi bi-key"></i></span>
                        <input name="currentPassword" id="currentPassword" class="form-control" type="password" placeholder="Current Password">
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text" id="password"><i class="bi bi-key-fill"></i></span>
                        <input name="newPassword1" id="newPassword1" class="form-control" type="password" placeholder="New Password">
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text" id="password"><i class="bi bi-key-fill"></i></span>
                        <input name="newPassword2" id="newPassword2" class="form-control" type="password" placeholder="Verify New Password">
                    </div>

                    <div class="d-grid">
                        <input type="submit" class="btn btn-primary" value="Verify" name="user_password_change_button">
                    </div>
                    <br>
                </form>
                <?php include "../settings/db_connection.php"; ?>
                <?php include "../controllers/validations.php"; ?>
            </div>
        </div>
    </div>
</body>

</html>
