<!DOCTYPE html>

<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icono.png">
    <title>Code Validation</title>
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
        <img src="../images/Farmaceutico%20en%20el%20mostrador%20con%20cliente.png" style="width:550px" alt="">
        <div class="col bg-white p-5 rounded bg">
            <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
            <h4 class="fw-bold text-center ру-5">Password Reset</h4>
            <br>
            <br>
            <form method="post" action="">
                <div class="input-group mb-4">
                    <span class="input-group-text" id="code"><i class="bi bi-patch-check-fill"></i></span>
                    <input name="code" id="code" class="form-control" placeholder="Enter the Reset Code" maxlength="10">
                </div>
                <div class="d-grid">
                    <input type="submit" class="btn btn-primary btn-block" value="Verify Code" name="verify_code_button">
                </div>
                <br>
                <div class="d-grid">
                    <input type="submit" class="btn btn-secondary btn-block" value="Resend Code" name="resend_code_button">
                </div>
                <br>
                <?php include "../settings/db_connection.php";?>
                <?php include "../controllers/validations.php";?>
            </form>
        </div>
    </div>
</div>
</body>
</html>