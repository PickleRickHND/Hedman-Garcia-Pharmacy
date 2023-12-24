<?php
session_start();
if (!empty($_SESSION["id"])) {
    header("Location: screens/home.php");
    exit;
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <style>
        body {
            background: linear-gradient(to right, #7dc8dd, #5794c0);
            margin-top: 8%;
        }
    </style>

</head>

<body>
    <div class="container w-75 bg-white mt-5 rounded shadow">
        <div class="row align-items-center">
            <img src="images/loginImage.png" style="width:550px; display: block; margin-left: auto; margin-right: auto;" alt="">
            <div class="col bg-white p-5 rounded bg">
                <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
                <h2 class="fw-bold text-center ру-5">Welcome</h2>
                <br>
                <form method="post" action="">
                    <div class="input-group mb-4">
                        <span class="input-group-text" id="email"><i class="bi bi-person-fill"></i></span>
                        <input name="email" id="email" class="form-control" type="text" placeholder="Email" maxlength="30" />
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text" id="password"><i class="bi bi-key-fill"></i></span>
                        <input name="password" id="password" class="form-control" type="password" placeholder="Password" maxlength="20">
                    </div>

                    <div class="fw-bold text-center d-grid">
                        <input type="submit" class="btn btn-primary btn-block" value="Sign In" name="login_button">
                    </div>

                    <div class="my-3">
                        <span><a href="screens/reset_password.php">Forgot Password?</a> </span><br>
                    </div>

                    <?php include "settings/db_connection.php"; ?>
                    <?php include "controllers/validations.php"; ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>