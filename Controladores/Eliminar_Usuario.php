<?php

include "../Configuracion/Conexion.php";
global $conexion;

if(!empty($_GET['id'])){
    $id=$_GET['id'];
    $eliminar=$conexion->query("DELETE FROM Usuarios WHERE id=$id");
    if ($eliminar == TRUE){
        echo "<div class= 'alert alert-success'>Usuario Eliminado Correctamente!</div>";
    }else{
        echo "<div class= 'alert alert-danger'>Ah ocurrido un Error al Eliminar este Usuario!</div>";
    }?>

    <script>
        (function() {
            var not = function () {
                window.history.replaceState(null, null, window.location.pathname);
            }
            setTimeout(not, 0)
        }())
    </script>

<?php }
    ?>


