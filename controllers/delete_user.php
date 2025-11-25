<?php
session_start();

// Security: Check if user is logged in
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;

if (!empty($_GET['id'])) {
    try {
        $id = intval($_GET['id']);

        // Security: Prevent user from deleting themselves
        if ($id === intval($_SESSION["id"])) {
            echo "<div class='alert alert-danger'>You cannot delete your own account.</div>";
            exit;
        }

        // Check if user exists before deletion
        $stmt_check = $connection->prepare("SELECT id FROM Usuarios WHERE id = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 0) {
            echo "<div class='alert alert-warning'>User not found or already deleted.</div>";
            $stmt_check->close();
        } else {
            $stmt_check->close();

            // Use prepared statement to prevent SQL injection
            $stmt = $connection->prepare("DELETE FROM Usuarios WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>User deleted Successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>An Error has occurred while deleting this user: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }
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