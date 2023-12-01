<?php
session_start();
if(empty($_SESSION["id"])){
    header("Location: ../index.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;

$id_session = $_SESSION["id"];
$query_seller = $connection->query("SELECT * FROM Usuarios WHERE id='$id_session'");
$query_paymentMethod = $connection->query("SELECT formas_pago FROM Metodos_Pago");
global $data;
?>

<!DOCTYPE html>

<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <title>Billing Module</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/js/tempusdominus-bootstrap-4.min.js"></script>

    <style>
        body{
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>


</head>
<body style="margin-bottom: 7%">
<div class="container w-90 bg-white mt-5 rounded shadow">
    <div class="row align-items-center align-items-stretch">
        <div class="col bg-white p-5 rounded bg">
            <h2 class="fw-bold text-center ру-5"><strong>Hedman Garcia Pharmacy</strong></h2><br>
            <h3 class="fw-bold text-center ру-5"><strong>Billing History</strong></h3>
            <br>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-small btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 20 20">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                    </svg> Generate New Receipt
                </button>
                    <input type="text" style="width: 300px" class="form-control" id="searchReceipt" placeholder="Search for Receipts">
            </div>

            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New Receipt: </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" >
                            <form id="receiptForm" method="post" action="">
                                <div class="mb-3">
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <label for="recipient-name1" class="col-form-label"></label>
                                            <input style="width: 250px" type="text" class="form-control" id="recipient-name1" placeholder="Name" name="name">
                                        </div>

                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <label for="recipient-name1" class="col-form-label"></label>
                                            <input style="width: 250px" type="text" class="form-control" id="recipient-name1" placeholder="RTN" name="rtn">
                                        </div>

                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <div class="input-group date" data-target-input="nearest">
                                                <div class="input-group-append">
                                                    <div onclick="actualDate()" class="input-group-text"><i class="bi bi-calendar-plus"></i></div>
                                                </div>
                                                <input onclick="actualDate()" value="Date & Time" id="actual-date" type="text" style="width: 192px" class="form-control" name="date_time_invoice" readonly/>
                                            </div>
                                        </div>
                                    </div>

                                        <?php
                                        while($fetch_seller = $query_seller->fetch_object()){
                                        ?>
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><i class="bi bi-person-badge-fill"></i></div>
                                                </div>
                                                <input style="width: 208px" type="text" class="form-control" value="<?=$fetch_seller->nombre . " " . $fetch_seller->apellido?>" name="cashier" readonly/>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        ?>

                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <select style="width: 250px" class="form-select" name="payment_method">
                                                <option>Payment Method</option>
                                                <?php
                                                while($fetch_PaymentMethod = $query_paymentMethod->fetch_object()) {
                                                    echo '<option value="' . $fetch_PaymentMethod->formas_pago . '">' . $fetch_PaymentMethod->formas_pago . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>


                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <div class="input-group date" data-target-input="nearest">
                                                <div class="input-group-append">
                                                    <div onclick="updateDate()" class="input-group-text"><i class="bi bi-calendar-x"></i></i></div>
                                                </div>
                                                <input onclick="updateDate()" value="Expiration" id="selected-date" type="text" style="width: 192px" class="form-control" placeholder="Expiration" name="expiration_date_invoice" readonly/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-small btn-warning" onclick="cleanBlanks()" style="width: 250px">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eraser" viewBox="0 0 20 20">
                                                <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414l-3.879-3.879zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
                                            </svg> Clean the blanks
                                        </button>
                                        <input style="width: 232px;" type="text" class="form-control" id="searchProductsReceipt" placeholder="Search for Products">
                                    </div>

                                    <div>
                                        <table class="table table-hover" id="tabla2">
                                            <thead>
                                            <tr>
                                                <th style="text-align:center" scope="col">Product N.º </th>
                                                <th style="text-align:center" scope="col">Name</th>
                                                <th style="text-align:center" scope="col">Quantity</th>
                                                <th style="text-align:center" scope="col">Price</th>
                                                <th style="text-align:center" scope="col">Presentation</th>
                                                <th style="text-align:center" scope="col">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            include "../settings/db_connection.php";
                                            global $connection;

                                            $productsPerPage = 3; 

                                            $totalProducts = $connection->query("SELECT COUNT(*) as total FROM Inventario")->fetch_assoc()['total'];
                                            $totalPages = ceil($totalProducts / $productsPerPage);

                                            $actualPage = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

                                            if ($actualPage < 1) {
                                                $actualPage = 1;
                                            } elseif ($actualPage > $totalPages) {
                                                $actualPage = $totalPages;
                                            }

                                            $initialIndex = ($actualPage - 1) * $productsPerPage;

                                            $sql = "SELECT * FROM Inventario LIMIT $productsPerPage OFFSET $initialIndex";
                                            $result = $connection->query($sql);

                                            while($data=$result->fetch_object()){ ?>

                                                <tr>
                                                    <td style="text-align:center"><?= $data->id_producto?></td>
                                                    <td style="text-align:center"><?= $data->nombre_producto?></td>
                                                    <td class="d-flex align-items-center justify-content-center"><input class="form-control" style="width: 60px;"></td>
                                                    <td style="text-align:center; white-space: nowrap;"><?= ("Lps. " . $data->precio)?></td>
                                                    <td style="text-align:center"><?= $data->presentacion_producto?></td>
                                                    <td class="fw-bold text-center">
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <a class="btn btn-primary">
                                                                <span class="d-flex align-items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                                                                        <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                                                                        <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        </div>
                                                </tr>
                                            <?php }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-success" value="Generate Receipt" name="new_billing_button">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php include "../settings/db_connection.php";?>
            <?php include "../controllers/validations.php";?>

            <table class="table table-hover" id="tabla1">
                <thead>
                <tr>
                    <th style="text-align:center" scope="col">Receipt N.º</th>
                    <th style="text-align:center" scope="col">Date / Time</th>
                    <th style="text-align:center" scope="col">Client </th>
                    <th style="text-align:center" scope="col">RTN</th>
                    <th style="text-align:center" scope="col">Cashier </th>
                    <th style="text-align:center" scope="col">Status</th>
                    <th style="text-align:center" scope="col">Payment Method</th>
                    <th style="text-align:center" scope="col">Total</th>
                    <th style="text-align:center" scope="col">Actions</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                include "../settings/db_connection.php";
                global $connection;

                $receiptsPerPage = 5;

                $totalReceipts = $connection->query("SELECT COUNT(*) as total FROM Facturas")->fetch_assoc()['total'];
                $totalPages = ceil($totalReceipts / $receiptsPerPage);

                $actualPage = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

                if ($actualPage < 1) {
                    $actualPage = 1;
                } elseif ($actualPage > $totalPages) {
                    $actualPage = $totalPages;
                }

                $initialIndex = ($actualPage - 1) * $receiptsPerPage;

                $sql = "SELECT * FROM Facturas LIMIT $receiptsPerPage OFFSET $initialIndex";
                $result = $connection->query($sql);

                while($data=$result->fetch_object()){ ?>

                    <tr>
                        <td style="text-align:center"><?= $data->id_factura?></td>
                        <td style="text-align:center"><?= $data->fecha_hora?></td>
                        <td style="text-align:center"><?= $data->cliente?></td>
                        <td style="text-align:center"><?= $data->rtn?></td>
                        <td style="text-align:center"><?= $data->cajero?></td>
                        <td style="text-align:center"><?= $data->estado?></td>
                        <td style="text-align:center"><?= $data->metodo_pago?></td>
                        <td style="text-align:center"><?= "Lps. " . $data->total?></td>
                        <td class="fw-bold text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <a onclick="" class="btn btn-primary">
                                <span class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                                </span>
                                </a>
                                <div class="mx-1"></div>
                                <a onclick="" class="btn btn-danger">
                                <span class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                    </svg>
                                </span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php }
                ?>
                </tbody>
            </table>
            <br>
            <div class="text-center">
                <?php if ($actualPage > 1): ?>
                    <a href="?pagina=<?php echo $actualPage - 1; ?>" class="btn btn-primary">Previous</a>
                <?php endif; ?>

                <?php if ($actualPage < $totalPages): ?>
                    <a href="?pagina=<?php echo $actualPage + 1; ?>" class="btn btn-primary">Next</a>
                <?php endif; ?>
            </div>
            <br>
        </div>
    </div>
</div>
</body>
</html>
<script src="../controllers/functions.js"></script>