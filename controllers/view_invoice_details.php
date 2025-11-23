<?php
/**
 * View Invoice Details
 * This endpoint returns invoice header and line items
 */

session_start();
include "../settings/db_connection.php";
global $connection;

// Set response header to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'invoice' => null,
    'items' => []
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['id'])) {
        $response['message'] = 'Usuario no autenticado. Por favor inicie sesión.';
        echo json_encode($response);
        exit;
    }

    // Validate required GET parameter
    if (!isset($_GET['factura_id'])) {
        $response['message'] = 'Parámetro faltante: factura_id';
        echo json_encode($response);
        exit;
    }

    $factura_id = intval($_GET['factura_id']);

    // Get invoice header
    $stmt_invoice = $connection->prepare("SELECT id_factura, fecha_hora, cliente, rtn, cajero, estado, metodo_pago, total FROM Facturas WHERE id_factura = ?");
    $stmt_invoice->bind_param("i", $factura_id);
    $stmt_invoice->execute();
    $invoice_result = $stmt_invoice->get_result();

    if ($invoice_result->num_rows == 0) {
        $response['message'] = 'Factura no encontrada.';
        echo json_encode($response);
        exit;
    }

    $response['invoice'] = $invoice_result->fetch_assoc();
    $stmt_invoice->close();

    // Get invoice items
    $stmt_items = $connection->prepare("SELECT producto_id, nombre_producto, cantidad, precio_unitario, subtotal FROM Factura_Detalles WHERE factura_id = ? ORDER BY id");
    $stmt_items->bind_param("i", $factura_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();

    while ($item = $items_result->fetch_assoc()) {
        $response['items'][] = $item;
    }
    $stmt_items->close();

    $response['success'] = true;
    $response['message'] = 'Datos obtenidos exitosamente.';

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
