<?php

include "../settings/db_connection.php";
global $connection;

if (!empty($_GET['id_producto'])) {
    try {
        $id = intval($_GET['id_producto']);

        // Use prepared statement to prevent SQL injection
        $stmt = $connection->prepare("DELETE FROM Inventario WHERE id_producto = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<div class='alert alert-success'>Product Deleted Successfully!</div>";
            } else {
                echo "<div class='alert alert-warning'>Product not found or already deleted.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>An Error occurred while deleting this Product: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>An Error occurred: " . $e->getMessage() . "</div>";
    } ?>

    <script>
        //Modifica la URL despues de realizar el proceso que en este caso es eliminar!
        (function() {
            var not = function() {
                window.history.replaceState(null, null, window.location.pathname);
            }
            setTimeout(not, 0)
        }())
    </script>

<?php }
?>