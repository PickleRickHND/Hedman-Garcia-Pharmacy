<?php
/**
 * Add Product to Shopping Cart
 * This endpoint handles AJAX requests to add products to the shopping cart
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
    if (!isset($_POST['id_product']) || !isset($_POST['product_name']) || !isset($_POST['quantityToAdd']) || !isset($_POST['price_product'])) {
        $response['message'] = 'Parámetros faltantes. Se requiere: id_product, product_name, quantityToAdd, price_product';
        echo json_encode($response);
        exit;
    }

    // Sanitize and validate input
    $product_id = intval($_POST['id_product']);
    $product_name = trim($_POST['product_name']);
    $quantity_to_add = intval($_POST['quantityToAdd']);
    $price = floatval($_POST['price_product']); // NOTE: This will be overridden with DB price for security

    // Validate quantity
    if ($quantity_to_add <= 0) {
        $response['message'] = 'La cantidad debe ser mayor a cero.';
        echo json_encode($response);
        exit;
    }

    // Validate price
    if ($price < 0) {
        $response['message'] = 'El precio no puede ser negativo.';
        echo json_encode($response);
        exit;
    }

    // Verify product exists and has enough stock
    $stmt_check = $connection->prepare("SELECT id_producto, nombre_producto, cantidad_producto, precio FROM Inventario WHERE id_producto = ? AND active = TRUE");
    $stmt_check->bind_param("i", $product_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        $response['message'] = 'Producto no encontrado o inactivo.';
        echo json_encode($response);
        exit;
    }

    $product_data = $result_check->fetch_assoc();
    $stmt_check->close();

    // SECURITY: Always use database price, never trust client-supplied price
    $price = floatval($product_data['precio']);

    // Check available stock
    if ($product_data['cantidad_producto'] < $quantity_to_add) {
        $response['message'] = 'Stock insuficiente. Disponible: ' . $product_data['cantidad_producto'];
        echo json_encode($response);
        exit;
    }

    // Check if product is already in cart
    $stmt_cart_check = $connection->prepare("SELECT id, cantidad FROM Shopping_Cart WHERE usuario_id = ? AND producto_id = ?");
    $stmt_cart_check->bind_param("ii", $user_id, $product_id);
    $stmt_cart_check->execute();
    $cart_result = $stmt_cart_check->get_result();

    if ($cart_result->num_rows > 0) {
        // Product already in cart - update quantity
        $cart_item = $cart_result->fetch_assoc();
        $new_quantity = $cart_item['cantidad'] + $quantity_to_add;

        // Check if new quantity exceeds stock
        if ($new_quantity > $product_data['cantidad_producto']) {
            $response['message'] = 'La cantidad total excedería el stock disponible (' . $product_data['cantidad_producto'] . ')';
            echo json_encode($response);
            exit;
        }

        // Update cart item quantity
        $subtotal = $new_quantity * $price;
        $stmt_update = $connection->prepare("UPDATE Shopping_Cart SET cantidad = ?, precio_unitario = ?, subtotal = ? WHERE id = ?");
        $stmt_update->bind_param("iddi", $new_quantity, $price, $subtotal, $cart_item['id']);

        if (!$stmt_update->execute()) {
            throw new Exception('Error al actualizar el carrito: ' . $stmt_update->error);
        }
        $stmt_update->close();

        $response['message'] = 'Cantidad actualizada en el carrito.';
    } else {
        // Product not in cart - insert new item
        $subtotal = $quantity_to_add * $price;
        $stmt_insert = $connection->prepare("INSERT INTO Shopping_Cart (usuario_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("iisidd", $user_id, $product_id, $product_name, $quantity_to_add, $price, $subtotal);

        if (!$stmt_insert->execute()) {
            throw new Exception('Error al agregar al carrito: ' . $stmt_insert->error);
        }
        $stmt_insert->close();

        $response['message'] = 'Producto agregado al carrito exitosamente.';
    }

    $stmt_cart_check->close();

    // Calculate new cart total
    $stmt_total = $connection->prepare("SELECT SUM(subtotal) as total FROM Shopping_Cart WHERE usuario_id = ?");
    $stmt_total->bind_param("i", $user_id);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result();
    $total_data = $total_result->fetch_assoc();
    $stmt_total->close();

    $response['success'] = true;
    $response['cart_total'] = floatval($total_data['total'] ?? 0.00);

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