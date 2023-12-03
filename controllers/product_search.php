<?php
include "../settings/db_connection.php";
global $connection;

if (isset($_POST['searchText'])) {
    $searchText = $_POST['searchText'];

    // Realiza una consulta para buscar products por nombre o cualquier otro criterio que desees
    $query = "SELECT * FROM Inventario WHERE nombre_producto LIKE '%$searchText%' OR id_producto LIKE '%$searchText%' OR precio LIKE '%$searchText%' OR presentacion_producto LIKE '%$searchText%' OR descripcion LIKE '%$searchText%'";
    $result = $connection->query($query);

    // Inicializa un array para almacenar los resultados
    $products = array();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Convierte el array en formato JSON y envíalo
    echo json_encode($products);
}
