<?php

include "../settings/db_connection.php";
global $connection;

if(!empty($_GET['id_producto'])){
    $id=$_GET['id_producto'];
    $eliminar=$connection->query("DELETE FROM Inventario WHERE id_producto=$id");
    if ($eliminar == TRUE){
        echo "<div class= 'alert alert-success'>Product Deleted Succesfully!</div>";
    }else{
        echo "<div class= 'alert alert-danger'>An Error occurred while deleting this Product!</div>";
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