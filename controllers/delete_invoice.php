<?php
/**
 * Delete Invoice
 * This endpoint handles deletion of invoices
 * Note: Only admins or authorized users should be able to delete invoices
 */

require_once __DIR__ . "/../settings/session_config.php";
include "../settings/db_connection.php";
global $connection;

// Set response header to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['id'])) {
        $response['message'] = 'Usuario no autenticado. Por favor inicie sesión.';
        echo json_encode($response);
        exit;
    }

    // RBAC: Only Administrador can delete invoices
    $user_role = isset($_SESSION['roles']) ? $_SESSION['roles'] : '';
    if ($user_role !== 'Administrador') {
        $response['message'] = 'Acceso denegado. Solo los administradores pueden eliminar facturas.';
        echo json_encode($response);
        exit;
    }

    $user_id = intval($_SESSION['id']);

    // Validate required POST parameter
    if (!isset($_POST['factura_id'])) {
        $response['message'] = 'Parámetro faltante: factura_id';
        echo json_encode($response);
        exit;
    }

    $factura_id = intval($_POST['factura_id']);

    // Start transaction
    $connection->begin_transaction();

    // Check if invoice exists and get details
    $stmt_check = $connection->prepare("SELECT id_factura, estado FROM Facturas WHERE id_factura = ?");
    $stmt_check->bind_param("i", $factura_id);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows == 0) {
        throw new Exception('Factura no encontrada.');
    }

    $invoice_data = $check_result->fetch_assoc();
    $stmt_check->close();

    // Get invoice items to restore inventory
    $stmt_items = $connection->prepare("SELECT producto_id, cantidad FROM Factura_Detalles WHERE factura_id = ?");
    $stmt_items->bind_param("i", $factura_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();

    // Restore inventory for each product
    $stmt_restore = $connection->prepare("UPDATE Inventario SET cantidad_producto = cantidad_producto + ? WHERE id_producto = ?");

    while ($item = $items_result->fetch_assoc()) {
        $stmt_restore->bind_param("ii", $item['cantidad'], $item['producto_id']);
        if (!$stmt_restore->execute()) {
            throw new Exception('Error al restaurar inventario para producto ID: ' . $item['producto_id']);
        }
    }

    $stmt_items->close();
    $stmt_restore->close();

    // Delete invoice details (will cascade if foreign key is set, but doing it explicitly for safety)
    $stmt_delete_details = $connection->prepare("DELETE FROM Factura_Detalles WHERE factura_id = ?");
    $stmt_delete_details->bind_param("i", $factura_id);

    if (!$stmt_delete_details->execute()) {
        throw new Exception('Error al eliminar detalles de factura: ' . $stmt_delete_details->error);
    }
    $stmt_delete_details->close();

    // Delete invoice header
    $stmt_delete_invoice = $connection->prepare("DELETE FROM Facturas WHERE id_factura = ?");
    $stmt_delete_invoice->bind_param("i", $factura_id);

    if (!$stmt_delete_invoice->execute()) {
        throw new Exception('Error al eliminar factura: ' . $stmt_delete_invoice->error);
    }
    $stmt_delete_invoice->close();

    // Commit transaction
    $connection->commit();

    $response['success'] = true;
    $response['message'] = 'Factura #' . $factura_id . ' eliminada exitosamente. Inventario restaurado.';

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($connection)) {
        $connection->rollback();
    }
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
