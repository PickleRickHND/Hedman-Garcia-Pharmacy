<?php

include "../Configuracion/Conexion.php";
global $conexion;

if(!empty($_GET['id_producto'])){
    $id=$_GET['id_producto'];
    $eliminar=$conexion->query("DELETE FROM Inventario WHERE id_producto=$id");
    if ($eliminar == TRUE){
        echo "<div class= 'alert alert-success'>Producto Eliminado Correctamente!</div>";
    }else{
        echo "<div class= 'alert alert-danger'>Ah ocurrido un Error al Eliminar este Producto!</div>";
    }?>

    <script>
        //Modifica la URL despues de realizar el proceso que en este caso es eliminar!
        (function() {
            var not = function () {
                window.history.replaceState(null, null, window.location.pathname);
            }
            setTimeout(not, 0)
        }())
    </script>

<?php }
?>