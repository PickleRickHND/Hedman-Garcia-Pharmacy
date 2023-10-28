<?php
session_start();
if(empty($_SESSION["id"])){
    header("Location: ../index.php");
    exit;
}

include "../Configuracion/Conexion.php";
global $conexion;

$id=$_GET['id_producto'];
$seleccionar=$conexion->query("SELECT * FROM Inventario WHERE id_producto='$id'");

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
        body{
            background: linear-gradient(to right, #7dc8dd, #5794c0);
        }
    </style>

</head>
<body>
<div class="container w-75 bg-white mt-5 rounded shadow">
    <div class="row align-items-center align-items-stretch">
        <div class="col bg-white p-5 rounded bg">
            <h2 class="fw-bold text-center ру-5"><strong>Farmacia Hedman Garcia</strong></h2><br>
            <h3 class="fw-bold text-center ру-5">Editar Producto</h3>
            <br>
            <div>
                <form method="post" action="">
                    <?php
                    include "../Controladores/EditarProducto.php";
                    while ($datos=$seleccionar->fetch_object()){ ?>

                        <div class="mb-3">
                            <input type="hidden" name="id_producto" value="<?= $_GET['id_producto']?>">
                            <br>
                            <label for="recipient-name1" class="col-form-label">N.º de Producto:</label>
                            <input type="text" class="form-control" maxlength="6" id="recipient-name1" placeholder="N.º de Producto" name="number" value="<?= $datos->id_producto?>">
                            <br>
                            <label for="recipient-name1" class="col-form-label">Nombre:</label>
                            <input type="text" class="form-control" id="recipient-name1" placeholder="Nombre" name="name" value="<?= $datos->nombre_producto?>">
                            <br>
                            <label for="recipient-name2" class="col-form-label">Descripción:</label>
                            <textarea style="height: 120px;" type="text" class="form-control" id="recipient-name2"  name="description"><?= $datos->descripcion?></textarea>
                            <br>
                            <label for="recipient-name3" class="col-form-label">Existencia:</label>
                            <input type="number" min="0" max="200" class="form-control" id="recipient-name3" placeholder="Cantidad" name="quantity" value="<?= $datos->existencia_producto?>">
                            <br>
                            <label for="recipient-name5" class="col-form-label">Precio:</label>
                            <input type="text" min="0" max="100000" class="form-control" id="recipient-name5" placeholder="Precio" name="price" value="<?= $datos->precio?>">
                            <br>
                            <label for="recipient-name6" class="col-form-label">Presentación:</label>
                            <input type="text" class="form-control" id="recipient-name6" placeholder="Presentación" name="presentation" value="<?= $datos->presentacion_producto?>">
                            <br>

                            <div>
                                <label for="recipient-name7" class="col-form-label">Fecha de Vencimiento:</label>
                                <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                                    <div class="input-group-append " data-target="#datetimepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="bi bi-calendar-plus-fill"></i></div>
                                    </div>
                                    <input type="text" style="width: 189px" class="form-control datetimepicker-input" id="recipient-name7" data-target="#datetimepicker" name="expiration_date" value="<?= $datos->fecha_vencimiento?>"/>
                                </div>

                                <script>
                                    $(document).ready(function () {
                                        $('#datetimepicker').datetimepicker({
                                            format: 'DD-MM-YYYY',
                                        });
                                    });
                                </script>
                            </div>

                            <br>
                            <label for="recipient-name8" class="col-form-label">Forma de Administración:</label>
                            <input type="text" class="form-control" id="recipient-name8" placeholder="Forma de Administración" name="administration_form" value="<?= $datos->forma_administracion?>">
                            <br>
                            <label for="recipient-name9" class="col-form-label">Almacenamiento:</label>
                            <input type="text" class="form-control" id="recipient-name9" placeholder="Almacenamiento" name="storage" value="<?= $datos->almacenamiento?>">
                        </div>

                    <?php }
                    ?>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-danger" href="Control_Inventario.php">Cerrar</a>
                        <input type="submit" class="btn btn-primary" value="Modificar" name="editarproductobtn">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>