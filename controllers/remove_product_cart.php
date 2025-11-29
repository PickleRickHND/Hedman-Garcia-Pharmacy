<?php
/**
 * Remove Product from Shopping Cart
 * This endpoint handles AJAX requests to remove products from the shopping cart
 */

require_once __DIR__ . "/../settings/session_config.php";
include "../settings/db_connection.php";
global $connection;

// Set response header to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'cart_total' => 0.00
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['id'])) {
        $response['message'] = 'Usuario no autenticado. Por favor inicie sesión.';
        echo json_encode($response);
        exit;
    }

    $user_id = intval($_SESSION['id']);

    // Validate required POST parameters
    if (!isset($_POST['producto_id'])) {
        $response['message'] = 'Parámetro faltante: producto_id';
        echo json_encode($response);
        exit;
    }

    $product_id = intval($_POST['producto_id']);

    // Delete product from cart
    $stmt_delete = $connection->prepare("DELETE FROM Shopping_Cart WHERE usuario_id = ? AND producto_id = ?");
    $stmt_delete->bind_param("ii", $user_id, $product_id);

    if (!$stmt_delete->execute()) {
        throw new Exception('Error al eliminar el producto del carrito: ' . $stmt_delete->error);
    }

    $affected_rows = $stmt_delete->affected_rows;
    $stmt_delete->close();

    if ($affected_rows == 0) {
        $response['message'] = 'Producto no encontrado en el carrito.';
    } else {
        $response['message'] = 'Producto eliminado del carrito exitosamente.';
        $response['success'] = true;
    }

    // Calculate new cart total
    $stmt_total = $connection->prepare("SELECT COALESCE(SUM(subtotal), 0.00) as total FROM Shopping_Cart WHERE usuario_id = ?");
    $stmt_total->bind_param("i", $user_id);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result();
    $total_data = $total_result->fetch_assoc();
    $stmt_total->close();

    $response['cart_total'] = floatval($total_data['total']);

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    // Ensure connection is closed
    if (isset($connection) && $connection) {
        $connection->close();
    }
}

// Return JSON response
echo json_encode($response);
?>
