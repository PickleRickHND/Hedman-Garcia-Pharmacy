<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <title>Inventory Control</title>

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

<body style="margin-bottom: 7%">
    <div class="container w-100 bg-white mt-5 rounded shadow">
        <div class="row align-items-center align-items-stretch">

            <div class="col bg-white p-5 rounded bg">

                <div class="fw-bold text-center d-grid">
                    <button type="button" id="goBackButton" class="btn btn-small btn-secondary btn-block" style="margin-top: 20px; width: fit-content;" onclick="goBack()"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
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

                <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
                <h3 class="fw-bold text-center ру-5"><strong>Inventory Control</strong></h3>
                <br>

                <!-- Button trigger modal -->
                <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <button type="button" class="btn btn-small btn-primary" data-toggle="modal" data-target="#newUserModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 20 20">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z" />
                        </svg> Add New Product
                    </button>
                    <input type="text" style="width: 300px" class="form-control" id="searchProduct" placeholder="Search for Products">
                </div>

                <style>
                    @media (max-width: 1000px) {
                        .btn-primary {
                            width: 100%;
                            display: none;
                        }

                        #searchProduct {
                            margin-top: 10px;
                            width: 100%;
                            /* Updated width to 100% */
                        }
                    }
                </style>

                <?php include "../settings/db_connection.php"; ?>
                <?php include "../controllers/validations.php"; ?>
                <?php include "../controllers/delete_product.php"; ?>


                <div class="modal fade bd-example-modal-lg" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add New Product:</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="">
                                    <div class="mb-3">

                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label for="recipient-name1" class="col-form-label"></label>
                                                <input style="width: 240px" type="text" maxlength="6" class="form-control" id="recipient-name1" placeholder="Product N.º" name="numero">
                                            </div>

                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label for="recipient-name1" class="col-form-label"></label>
                                                <input style="width: 240px" type="text" class="form-control" id="recipient-name1" placeholder="Name" name="name">
                                            </div>

                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <br>
                                                <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                                                    <div class="input-group-append " data-target="#datetimepicker" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="bi bi-calendar-plus-fill"></i></div>
                                                    </div>
                                                    <input type="text" style="width: 200px" class="form-control datetimepicker-input" data-target="#datetimepicker" name="expiration_date" placeholder="Expiration Date" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <div class="mb-3 d-flex flex-column align-items-start">
                                                <label for="recipient-name2" class="col-form-label"></label>
                                                <textarea style="height: 120px; vertical-align: top; width: 503px" class="form-control" id="recipient-name2" placeholder="Description" name="description"></textarea>
                                            </div>

                                            <div class="mb-3 d-flex flex-column align-items-center">
                                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                                    <label for="recipient-name3" class="col-form-label"></label>
                                                    <input style="width: 240px; " type="number" min="0" max="200" class="form-control" id="recipient-name3" placeholder="Existence" name="quantity">
                                                </div>

                                                <label for="recipient-name5" class="col-form-label"></label>
                                                <input style="width: 240px" type="text" class="form-control" id="recipient-name5" placeholder="Price" name="price">
                                            </div>
                                        </div>

                                        <div class="mb-3 d-flex justify-content-between align-items-center">

                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label for="recipient-name6" class="col-form-label"></label>
                                                <input style="width: 240px" type="text" class="form-control" id="recipient-name6" placeholder="Presentation" name="presentation">
                                            </div>

                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label for="recipient-name8" class="col-form-label"></label>
                                                <input style="width: 240px" type="text" class="form-control" id="recipient-name8" placeholder="Way of Administration" name="administration_form">
                                            </div>

                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label for="recipient-name9" class="col-form-label"></label>
                                                <input style="width: 240px" type="text" class="form-control" id="recipient-name9" placeholder="Storage" name="storage">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-primary" value="Save" name="save_product_button">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabla1" class="table table-hover">
                        <thead>
                            <tr>
                                <th style="text-align:center" scope="col">Product N.º</th>
                                <th style="text-align:center" scope="col">Name</th>
                                <th style="text-align:center" scope="col">Description</th>
                                <th style="text-align:center" scope="col">Existence</th>
                                <th style="text-align:center" scope="col">Price</th>
                                <th style="text-align:center" scope="col">Presentation</th>
                                <th style="text-align:center" scope="col">Expiration Date</th>
                                <th style="text-align:center" scope="col">Way of Administration</th>
                                <th style="text-align:center" scope="col">Storage</th>
                                <th style="text-align:center" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../settings/db_connection.php";
                            global $connection;

                            $productsPerPage = 4;

                            $totalProducts = $connection->query("SELECT COUNT(*) as total FROM Inventario")->fetch_assoc()['total'];
                            $totalPages = ceil($totalProducts / $productsPerPage);

                            $paginaActual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

                            if ($paginaActual < 1) {
                                $paginaActual = 1;
                            } elseif ($paginaActual > $totalPages) {
                                $paginaActual = $totalPages;
                            }

                            $initialIndex = ($paginaActual - 1) * $productsPerPage;

                            $sql = "SELECT * FROM Inventario LIMIT $productsPerPage OFFSET $initialIndex";
                            $result = $connection->query($sql);

                            while ($data = $result->fetch_object()) { ?>

                                <tr>
                                    <td style="text-align:center"><?= htmlspecialchars($data->id_producto) ?></td>
                                    <td style="text-align:center"><?= htmlspecialchars($data->nombre_producto) ?></td>
                                    <td style="text-align: justify">
                                        <?php
                                        $descripcion = $data->descripcion;
                                        $shortDescription = substr($descripcion, 0, 75);
                                        $longDescription = $data->descripcion;

                                        echo '<span id="descripcion-corta-' . intval($data->id_producto) . '">' . htmlspecialchars($shortDescription) . '</span>';
                                        echo '<span id="descripcion-completa-' . intval($data->id_producto) . '" style="display:none;">' . htmlspecialchars($longDescription) . '</span>';

                                        if (strlen($descripcion) > 75) {
                                            echo '<button style=" font-size: 14px;" class="btn btn-small btn-info" onclick="showMore(' . $data->id_producto . ')">Show More...</button>';
                                            echo '<button class="btn btn-small btn-info" onclick="showLess(' . $data->id_producto . ')" style="display:none; font-size: 14px;">Show Less...</button>';
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align:center"><?= htmlspecialchars($data->cantidad_producto) ?></td>
                                    <td style="text-align:center; white-space: nowrap;"><?= ("Lps. " . htmlspecialchars($data->precio)) ?></td>
                                    <td style="text-align:center"><?= htmlspecialchars($data->presentacion_producto) ?></td>
                                    <td style="text-align:center"><?= htmlspecialchars($data->fecha_vencimiento) ?></td>
                                    <td style="text-align:center"><?= htmlspecialchars($data->forma_administracion) ?></td>
                                    <td style="text-align:center"><?= htmlspecialchars($data->almacenamiento) ?></td>
                                    <td class="fw-bold text-center">

                                        <a href="edit_product.php?id_producto=<?= $data->id_producto ?>" class="btn btn-small btn-warning btn-block">
                                            <span class="d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill me-1" viewBox="0 0 20 20">
                                                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z" />
                                                </svg> Edit
                                            </span>
                                        </a>

                                        <br>
                                        <a onclick="return deleteProduct()" href="inventory_control.php?id_producto=<?= $data->id_producto ?>" class="btn btn-small btn-danger btn-block">
                                            <span class="d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 20 20">
                                                    <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" />
                                                </svg> Delete
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                            ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="text-center">
                    <?php if ($paginaActual > 1) : ?>
                        <a href="?pagina=<?php echo $paginaActual - 1; ?>" class="btn btn-primary">Previous</a>
                    <?php endif; ?>

                    <?php if ($paginaActual < $totalPages) : ?>
                        <a href="?pagina=<?php echo $paginaActual + 1; ?>" class="btn btn-primary">Next</a>
                    <?php endif; ?>
                </div>
                <br>
            </div>
        </div>
    </div>
</body>

</html>
<script src="../controllers/functions.js"></script>