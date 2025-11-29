<?php
session_start();

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
    <title>Access Denied</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>
</head>

<body style="margin-top: 10%">
    <div class="container w-50 bg-white mt-5 rounded shadow p-5">
        <div class="text-center">
            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 5rem;"></i>
            <h1 class="fw-bold text-danger mt-4">Access Denied</h1>
            <p class="lead mt-3">You do not have permission to access this page.</p>
            <p class="text-muted">Please contact your administrator if you believe this is an error.</p>
            <div class="mt-4">
                <a href="home.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-house-fill"></i> Go to Home
                </a>
            </div>
        </div>
    </div>
</body>

</html>
