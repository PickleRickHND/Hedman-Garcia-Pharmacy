<?php

global $conexion;

if (!empty($_POST["editarproductobtn"])){
    if ((!empty($_POST["number"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))){
        $id=$_POST['id_producto'];
        $number = $conexion->real_escape_string($_POST["number"]);
        $description = $conexion->real_escape_string($_POST["description"]);
        $name = $conexion->real_escape_string($_POST["name"]);
        $quantity = $conexion->real_escape_string($_POST["quantity"]);
        $price = $conexion->real_escape_string($_POST["price"]);
        $presentation = $conexion->real_escape_string($_POST["presentation"]);
        $expiration_date = $conexion->real_escape_string($_POST["expiration_date"]);
        $administration_form = $conexion->real_escape_string($_POST["administration_form"]);
        $storage = $conexion->real_escape_string($_POST["storage"]);

        $check_number = $conexion->query("SELECT * FROM Inventario WHERE id_producto='$number'");
        if (mysqli_num_rows($check_number) > 0 && ($_GET['id_producto'] != $number)){
            echo "<div class= 'alert alert-danger'>El Número de Producto ingresado se encuentra asignado a otro producto!</div>";
        }else{
            if (preg_match("/^[0-9]+$/", $number) && (strlen($number) <= 6)){
                if ((strlen($description) <= 500) && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9\/!(),.]+$/u",$description)) {
                    if (strlen($name) <= 30 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                        if (strlen($quantity) <= 6 && preg_match("/^\d+$/", $quantity)){
                            if(strlen($price) <= 9 && preg_match("/^\d+(\.\d+)?$/", $price)){
                                if (strlen($presentation) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$presentation)) {
                                    if (preg_match("/^(?:\d{4}[-\/]\d{2}[-\/]\d{2}|\d{2}[-\/]\d{2}[-\/]\d{4})$/",$expiration_date)) {
                                        if (strlen($administration_form) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$administration_form)) {
                                            if (strlen($storage) <= 25 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$storage)) {
                                                $edit_product=("UPDATE Inventario SET id_producto='$number',nombre_producto='$name',descripcion='$description',existencia_producto='$quantity',precio='$price',presentacion_producto='$presentation',fecha_vencimiento='$expiration_date',forma_administracion='$administration_form',almacenamiento='$storage' WHERE id_producto='$id'");
                                                $response = mysqli_query($conexion, $edit_product);
                                                if ($response === TRUE) {
                                                    echo "<div class= 'alert alert-success'>Se ha Modificado el Producto Correctamente!</div>";
                                                    header("refresh:3;url=Control_Inventario.php");
                                                } else {
                                                    echo "<div class= 'alert alert-danger'>Se ha generado un Error al Modificar el Producto!</div>";
                                                }
                                            }else{
                                                echo "<div class= 'alert alert-danger'>Por favor ingrese una Forma de Almacenamiento Válida!</div>";
                                            }
                                        }else{
                                            echo "<div class= 'alert alert-danger'>Por favor ingrese una Forma de Administración Válida!</div>";
                                        }
                                    }else {
                                        echo "<div class= 'alert alert-danger'>Por favor ingrese una Fecha de Vencimiento Válida!</div>";
                                    }
                                }else{
                                    echo "<div class= 'alert alert-danger'>Por favor ingrese un tipo de Presentación Válido!</div>";
                                }
                            }else{
                                echo "<div class= 'alert alert-danger'>Por favor ingrese un Precio Válido!</div>";
                            }
                        }else{
                            echo "<div class= 'alert alert-danger'>Por favor ingrese una Cantidad Válida!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>El nombre es demasiado extenso o contiene caracteres invalidos, Porfavor ingrese un Nombre Válido!</div>";
                    }
                }else{
                    echo "<div class= 'alert alert-danger'>La Descripción es demasiado extensa o Contiene Caracteres Invalidos, Porfavor intente nuevamente!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>El Número de Producto es Inválido o contiene mas de 6 dígitos!</div>";
            }
        }
    }else{
        echo "<div class= 'alert alert-danger'>Porfavor Rellene Todos los Campos e Intentelo Nuevamente!</div>";
    }
}