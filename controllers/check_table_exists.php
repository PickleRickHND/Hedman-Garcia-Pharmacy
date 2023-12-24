<?php

include "../settings/db_connection.php";
global $connection;

// Obtiene el nombre de la tabla desde la solicitud AJAX
$tableName = $_POST['tableName'];

// Prepara una consulta SQL para verificar si la tabla existe
$query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'FarmaciaHG' AND table_name = '$tableName'";

// Ejecuta la consulta
$result = $connection->query($query);

// Prepara la respuesta
$response = array();
if ($result->fetch_row()[0] > 0) {
    $response = true;
} else {
    $response = false;
}

// Devolver la respuesta en formato JSON
echo json_encode($response);

// Cierra la conexión a la base de datos
$connection->close();
