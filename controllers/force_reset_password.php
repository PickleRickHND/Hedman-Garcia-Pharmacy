<?php
session_start();

// Security: Check if user is logged in
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

include "../settings/db_connection.php";
global $connection;

// Security: Verify user has admin role before allowing password reset
$current_user_id = intval($_SESSION["id"]);
$stmt_role = $connection->prepare("SELECT roles FROM Usuarios WHERE id = ?");
$stmt_role->bind_param("i", $current_user_id);
$stmt_role->execute();
$result_role = $stmt_role->get_result();
$user_data = $result_role->fetch_assoc();
$stmt_role->close();

if ($user_data['roles'] !== 'Administrador') {
    echo "<div class='alert alert-danger'>Access denied. Only administrators can reset passwords.</div>";
    exit;
}

//This Validation was done to Reset Users Password using a Full Admin Account
if (!empty($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        // Generate a secure random temporary password
        $newpassword = bin2hex(random_bytes(8)); // 16 character random password
        $cryptnewpassword = password_hash($newpassword, PASSWORD_DEFAULT);

        // Use prepared statement to prevent SQL injection
        $stmt = $connection->prepare("UPDATE Usuarios SET contrasena = ? WHERE id = ?");
        $stmt->bind_param("si", $cryptnewpassword, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                // Store temporary password in session to display to admin
                $_SESSION['temp_password'] = $newpassword;
                $_SESSION['temp_password_user_id'] = $id;
                header("Location: ../screens/user_management.php?password_reset=success");
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