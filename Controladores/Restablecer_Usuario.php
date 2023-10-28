<?php

include "../Configuracion/Conexion.php";
global $conexion;

if(!empty($_GET['id'])){
    $id=$_GET['id'];
    $newpassword='P@55W0RD!';
    $cryptnewpassword=password_hash($newpassword, PASSWORD_DEFAULT);
    $editPassword=$conexion->query("UPDATE Usuarios SET contrasena='$cryptnewpassword' WHERE id='$id'");
    if ($editPassword == TRUE){
        header( "Location: ../Pantallas/Administracion_Usuarios.php" );
        echo "<div class= 'alert alert-success'>Se ha Restablecido la Contraseña Correctaente!</div>";
    }else{
        echo "<div class= 'alert alert-danger'>Ah ocurrido un Error al Restablecer la Contraseña para este Usuario!</div>";
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