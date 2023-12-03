<?php

include "../settings/db_connection.php";
global $connection;

//This Validation was done to Reset Users Password using a Full Admin Account
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $newpassword = 'P@55W0RD';
    $cryptnewpassword = password_hash($newpassword, PASSWORD_DEFAULT);
    $editPassword = $connection->query("UPDATE Usuarios SET contrasena='$cryptnewpassword' WHERE id='$id'");
    if ($editPassword == TRUE) {
        header("Location: ../screens/user_management.php");
        echo "<div class= 'alert alert-success'>Password has been Reset Sucessfully!</div>";
    } else {
        echo "<div class= 'alert alert-danger'>An Error has occurred while resetting the users password!</div>";
    } ?>

    <script>
        (function() {
            var not = function() {
                window.history.replaceState(null, null, window.location.pathname);
            }
            setTimeout(not, 0)
        }())
    </script>

<?php }
?>