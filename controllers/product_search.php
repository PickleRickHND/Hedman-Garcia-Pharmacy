<?php
include "../settings/db_connection.php";
global $connection;

// Set response header to JSON
header('Content-Type: application/json');

if (isset($_POST['searchText'])) {
    try {
        $searchText = trim($_POST['searchText']);

        // Use prepared statement with LIKE to prevent SQL injection
        $searchParam = "%$searchText%";

        // Use prepared statement for security
        $stmt = $connection->prepare("SELECT id_producto, nombre_producto, descripcion, cantidad_producto, empaque_producto, precio, presentacion_producto, fecha_vencimiento, forma_administracion, almacenamiento FROM Inventario WHERE (nombre_producto LIKE ? OR id_producto LIKE ? OR precio LIKE ? OR presentacion_producto LIKE ? OR descripcion LIKE ?) AND active = TRUE");

        $stmt->bind_param("sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();

        // Inicializa un array para almacenar los resultados
        $products = array();

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();

        // Convierte el array en formato JSON y envíalo
        echo json_encode($products);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Missing searchText parameter']);
}
?>