<?php

include "../settings/db_connection.php";
global $connection;

if(!empty($_GET['id'])){
    $id=$_GET['id'];
    $eliminar=$connection->query("DELETE FROM Usuarios WHERE id=$id");
    if ($eliminar == TRUE){
        echo "<div class= 'alert alert-success'>User deleted Succesfully!</div>";
    }else{
        echo "<div class= 'alert alert-danger'>An Error has occured while deleting this user!</div>";
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


