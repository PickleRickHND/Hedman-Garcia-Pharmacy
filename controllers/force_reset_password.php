<?php

include "../settings/db_connection.php";
global $connection;

//This Validation was done to Reset Users Password using a Full Admin Account
if (!empty($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $newpassword = 'P@55W0RD';
        $cryptnewpassword = password_hash($newpassword, PASSWORD_DEFAULT);

        // Use prepared statement to prevent SQL injection
        $stmt = $connection->prepare("UPDATE Usuarios SET contrasena = ? WHERE id = ?");
        $stmt->bind_param("si", $cryptnewpassword, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                header("Location: ../screens/user_management.php");
                exit;
            } else {
                echo "<div class='alert alert-warning'>User not found or password is already set to default.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>An Error has occurred while resetting the users password: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>An Error occurred: " . $e->getMessage() . "</div>";
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