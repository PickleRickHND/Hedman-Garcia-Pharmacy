<?php
session_start();
if(empty($_SESSION["id"])){
    header("Location: ../index.php");
    exit;
}

include "../Configuracion/Conexion.php";
global $conexion;

$id_session = $_SESSION["id"];
$query_seller = $conexion->query("SELECT * FROM Usuarios WHERE id='$id_session'");
$query_paymentMethod = $conexion->query("SELECT formas_pago FROM Metodos_Pago");

?>

<!DOCTYPE html>

<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width-device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Imagenes/icono.png">
    <title>Farmacia HG</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../CSS/styles.css" type="text/css">

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
            <h2 class="fw-bold text-center ру-5"><strong>Farmacia Hedman Garcia</strong></h2><br>
            <h3 class="fw-bold text-center ру-5"><strong>Historial de Facturación</strong></h3>
            <br>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-small btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 20 20">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                    </svg> Crear Nueva Factura
                </button>
                    <input type="text" style="width: 300px" class="form-control" id="buscar-factura" placeholder="Buscar Factura">

            </div>

            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Nueva Factura: </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" >
                            <form id="formulario-factura" method="post" action="">
                                <div class="mb-3">
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <label for="recipient-name1" class="col-form-label"></label>
                                            <input style="width: 250px" type="text" class="form-control" id="recipient-name1" placeholder="Nombre" name="name">
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
                                                <input onclick="actualDate()" value="Fecha y Hora" id="actual-date" type="text" style="width: 192px" class="form-control" name="date_time_invoice" readonly/>
                                            </div>

                                            <script>
                                                function actualDate() {
                                                    var today = new Date();
                                                    today.setDate(today.getDate());

                                                    var day = today.getDate();
                                                    var month = today.getMonth();
                                                    var year = today.getFullYear();

                                                    var hours = today.getHours();
                                                    var minutes = today.getMinutes();
                                                    var seconds = today.getSeconds();

                                                    // Format the date and time
                                                    var formattedDate = (day < 10 ? "0" : "") + day + "-" + (month < 10 ? "0" : "") + month + "-" + year;
                                                    var formattedTime = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

                                                    // Combine date and time
                                                    var dateTime = formattedDate + " " + formattedTime;

                                                    document.getElementById("actual-date").value = dateTime;
                                                }
                                            </script>
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
                                                <option>Método de Pago</option>
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
                                                <input onclick="updateDate()" value="Vencimeinto" id="selected-date" type="text" style="width: 192px" class="form-control" placeholder="Vencimiento" name="expiration_date_invoice" readonly/>
                                            </div>

                                            <script>
                                                function updateDate() {
                                                    var today = new Date();
                                                    today.setDate(today.getDate() + 1);
                                                    var day = today.getDate();
                                                    var month = today.getMonth() + 1;
                                                    var year = today.getFullYear();
                                                    var formattedDate = (day < 10 ? "0" : "") + day + "-" + (month < 10 ? "0" : "") + month + "-" + year;
                                                    document.getElementById("selected-date").value = formattedDate;
                                                }
                                            </script>
                                        </div>
                                    </div>

                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-small btn-warning" onclick="limpiarCampos()" style="width: 250px">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eraser" viewBox="0 0 20 20">
                                                <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414l-3.879-3.879zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
                                            </svg> Borrar todos los campos
                                        </button>
                                        <input style="width: 232px;" type="text" class="form-control" id="buscar-producto" placeholder="Buscar Productos">

                                        <script>
                                            $(document).ready(function() {
                                                $("#buscar-producto").on("keyup", function() {
                                                    var searchText = $(this).val().toLowerCase();

                                                    // Realiza una solicitud AJAX para buscar productos
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "../Controladores/Busqueda_Productos.php",
                                                        data: { searchText: searchText },
                                                        dataType: "json",
                                                        success: function(response) {
                                                            // Borra la tabla de resultados actual
                                                            $("#tabla2 tbody").empty();

                                                            // Agrega los nuevos resultados a la tabla
                                                            for (var i = 0; i < response.length; i++) {
                                                                var producto = response[i];
                                                                var newRow = $("<tr>");
                                                                newRow.append("<td style='text-align:center'>" + producto.id_producto + "</td>");
                                                                newRow.append("<td style='text-align:center'>" + producto.nombre_producto + "</td>");
                                                                newRow.append("<td style='align-items: center' class='align-items-center justify-content-center'><input class='form-control' style='width: 60px;'>" + "</td>");
                                                                newRow.append("<td style='text-align:center; white-space: nowrap;'>Lps. " + producto.precio + "</td>");
                                                                newRow.append("<td style='text-align:center'>" + producto.presentacion_producto + "</td>");
                                                                newRow.append("<td style='text-align:center'><div class='d-flex justify-content-center align-items-center'> <a onclick='#' class='btn btn-primary'> <span class='d-flex align-items-center'> <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-cart-plus' viewBox='0 0 16 16'><path d='M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z'/><path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z'/></svg></span></a></div></td>");

                                                                $("#tabla2 tbody").append(newRow);
                                                            }
                                                        }
                                                    });
                                                });
                                            });
                                        </script>

                                        <script>
                                            function limpiarCampos() {
                                                document.getElementById("formulario-factura").reset();
                                            }
                                        </script>

                                    </div>

                                    <div>
                                        <table class="table table-hover" id="tabla2">
                                            <thead>
                                            <tr>
                                                <th style="text-align:center" scope="col">N.º de Producto</th>
                                                <th style="text-align:center" scope="col">Nombre</th>
                                                <th style="text-align:center" scope="col">Unidades</th>
                                                <th style="text-align:center" scope="col">Precio</th>
                                                <th style="text-align:center" scope="col">Presentación</th>
                                                <th style="text-align:center" scope="col">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            include "../Configuracion/Conexion.php";
                                            global $conexion;

                                            $productosPorPagina = 3; // Cambiar esto al número deseado de productos por página

                                            // Contar el número total de productos
                                            $totalProductos = $conexion->query("SELECT COUNT(*) as total FROM Inventario")->fetch_assoc()['total'];
                                            $totalPaginas = ceil($totalProductos / $productosPorPagina);

                                            $paginaActual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

                                            if ($paginaActual < 1) {
                                                $paginaActual = 1;
                                            } elseif ($paginaActual > $totalPaginas) {
                                                $paginaActual = $totalPaginas;
                                            }

                                            $indiceInicio = ($paginaActual - 1) * $productosPorPagina;

                                            $sql = "SELECT * FROM Inventario LIMIT $productosPorPagina OFFSET $indiceInicio";
                                            $resultado = $conexion->query($sql);

                                            while($datos=$resultado->fetch_object()){ ?>

                                                <script>
                                                    //Modifica la URL despues de realizar el proceso que en este caso es eliminar!
                                                    (function() {
                                                        var not = function () {
                                                            window.history.replaceState(null, null, window.location.pathname);
                                                        }
                                                        setTimeout(not, 0)
                                                    }())
                                                </script>

                                                <tr>
                                                    <td style="text-align:center"><?= $datos->id_producto?></td>
                                                    <td style="text-align:center"><?= $datos->nombre_producto?></td>
                                                    <td class="d-flex align-items-center justify-content-center"><input class="form-control" style="width: 60px;"></td>
                                                    <td style="text-align:center; white-space: nowrap;"><?= ("Lps. " . $datos->precio)?></td>
                                                    <td style="text-align:center"><?= $datos->presentacion_producto?></td>
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
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                    <input type="submit" class="btn btn-success" value="Generar Factura" name="generarfacturabtn">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php include "../Configuracion/Conexion.php";?>
            <?php include "../Controladores/Realizar_Facturacion.php";?>

            <table class="table table-hover" id="tabla1">
                <thead>
                <tr>
                    <th style="text-align:center" scope="col">N.º de Factura</th>
                    <th style="text-align:center" scope="col">Fecha / Hora</th>
                    <th style="text-align:center" scope="col">Cliente </th>
                    <th style="text-align:center" scope="col">RTN</th>
                    <th style="text-align:center" scope="col">Cajero </th>
                    <th style="text-align:center" scope="col">Estado</th>
                    <th style="text-align:center" scope="col">Método de pago</th>
                    <th style="text-align:center" scope="col">Total</th>
                    <th style="text-align:center" scope="col">Acciones</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                include "../Configuracion/Conexion.php";
                global $conexion;

                $facturasPorPagina = 5;

                // Contar el número total de productos
                $totalFacturas = $conexion->query("SELECT COUNT(*) as total FROM Facturas")->fetch_assoc()['total'];
                $totalPaginas = ceil($totalFacturas / $facturasPorPagina);

                $paginaActual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

                if ($paginaActual < 1) {
                    $paginaActual = 1;
                } elseif ($paginaActual > $totalPaginas) {
                    $paginaActual = $totalPaginas;
                }

                $indiceInicio = ($paginaActual - 1) * $facturasPorPagina;

                $sql = "SELECT * FROM Facturas LIMIT $facturasPorPagina OFFSET $indiceInicio";
                $resultado = $conexion->query($sql);

                while($datos=$resultado->fetch_object()){ ?>

                    <script>
                        //Modifica la URL despues de realizar el proceso que en este caso es eliminar!
                        (function() {
                            var not = function () {
                                window.history.replaceState(null, null, window.location.pathname);
                            }
                            setTimeout(not, 0)
                        }())
                    </script>

                    <tr>
                        <td style="text-align:center"><?= $datos->id_factura?></td>
                        <td style="text-align:center"><?= $datos->fecha_hora?></td>
                        <td style="text-align:center"><?= $datos->cliente?></td>
                        <td style="text-align:center"><?= $datos->rtn?></td>
                        <td style="text-align:center"><?= $datos->cajero?></td>
                        <td style="text-align:center"><?= $datos->estado?></td>
                        <td style="text-align:center"><?= $datos->metodo_pago?></td>
                        <td style="text-align:center"><?= "Lps. " . $datos->total?></td>
                        <td class="fw-bold text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <a onclick="#" class="btn btn-primary">
                                <span class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                                </span>
                                </a>
                                <div class="mx-1"></div>
                                <a onclick="#" class="btn btn-danger">
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
                <?php if ($paginaActual > 1): ?>
                    <a href="?pagina=<?php echo $paginaActual - 1; ?>" class="btn btn-primary">Anterior</a>
                <?php endif; ?>

                <?php if ($paginaActual < $totalPaginas): ?>
                    <a href="?pagina=<?php echo $paginaActual + 1; ?>" class="btn btn-primary">Siguiente</a>
                <?php endif; ?>
            </div>
            <br>
        </div>
    </div>
</div>
</body>
</html>