<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;

$id = isset($_GET['id_producto']) ? intval($_GET['id_producto']) : 0;
$stmt_product = $connection->prepare("SELECT * FROM Inventario WHERE id_producto = ?");
$stmt_product->bind_param("i", $id);
$stmt_product->execute();
$selectProduct = $stmt_product->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <title>Edit Product</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/js/tempusdominus-bootstrap-4.min.js"></script>

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
                <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
                <h3 class="fw-bold text-center ру-5">Edit Product</h3>
                <br>
                <div>
                    <form method="post" action="">
                        <?php
                        include "../controllers/validations.php";
                        while ($data = $selectProduct->fetch_object()) { ?>

                            <div class="mb-3">
                                <input type="hidden" name="id_producto" value="<?= $_GET['id_producto'] ?>">
                                <br>
                                <label for="recipient-name1" class="col-form-label">Product N.º :</label>
                                <input type="text" class="form-control" maxlength="6" id="recipient-name1" placeholder="Product N.º:" name="number" value="<?= $data->id_producto ?>">
                                <br>
                                <label for="recipient-name1" class="col-form-label">Name:</label>
                                <input type="text" class="form-control" id="recipient-name1" placeholder="Name" name="name" value="<?= $data->nombre_producto ?>">
                                <br>
                                <label for="recipient-name2" class="col-form-label">Description:</label>
                                <textarea style="height: 120px;" type="text" class="form-control" id="recipient-name2" name="description"><?= $data->descripcion ?></textarea>
                                <br>
                                <label for="recipient-name3" class="col-form-label">Existence:</label>
                                <input type="number" min="0" max="200" class="form-control" id="recipient-name3" placeholder="Cantidad" name="quantity" value="<?= $data->cantidad_producto ?>">
                                <br>
                                <label for="recipient-name5" class="col-form-label">Price:</label>
                                <input type="text" min="0" max="100000" class="form-control" id="recipient-name5" placeholder="Precio" name="price" value="<?= $data->precio ?>">
                                <br>
                                <label for="recipient-name6" class="col-form-label">Presentation:</label>
                                <input type="text" class="form-control" id="recipient-name6" placeholder="Presentación" name="presentation" value="<?= $data->presentacion_producto ?>">
                                <br>

                                <div>
                                    <label for="recipient-name7" class="col-form-label">Expiration Date:</label>
                                    <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                                        <div class="input-group-append " data-target="#datetimepicker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="bi bi-calendar-plus-fill"></i></div>
                                        </div>
                                        <input type="text" style="width: 189px" class="form-control datetimepicker-input" id="recipient-name7" data-target="#datetimepicker" name="expiration_date" value="<?= $data->fecha_vencimiento ?>" />
                                    </div>
                                </div>

                                <br>
                                <label for="recipient-name8" class="col-form-label">Administration Way:</label>
                                <input type="text" class="form-control" id="recipient-name8" placeholder="Forma de Administración" name="administration_form" value="<?= $data->forma_administracion ?>">
                                <br>
                                <label for="recipient-name9" class="col-form-label">Storage:</label>
                                <input type="text" class="form-control" id="recipient-name9" placeholder="Almacenamiento" name="storage" value="<?= $data->almacenamiento ?>">
                            </div>

                        <?php }
                        ?>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-danger" href="inventory_control.php">Close</a>
                            <input type="submit" class="btn btn-primary" value="Edit" name="edit_product_button">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script src="../controllers/functions.js"></script>