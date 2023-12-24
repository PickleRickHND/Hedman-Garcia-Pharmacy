<?php

include "../settings/db_connection.php";
global $connection;

// Obtiene el nombre de la tabla desde la solicitud AJAX
$tableName = $_POST['tableName'];
$productName = $_POST['product_name'];
$quantityToAdd = $_POST['quantityToAdd'];
$price_product = $_POST['price_product'];
$id_product = $_POST['id_product'];
$subtotal = $quantityToAdd * $price_product;

// Prepara una consulta SQL para verificar si la tabla existe
$query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'FarmaciaHG' AND table_name = '$tableName'";

// Ejecuta la consulta
$result = $connection->query($query);

// Prepara la respuesta
$response = array();

if ($result->fetch_row()[0] > 0) {
    $response = true;
    $addItem = "INSERT INTO " . $tableName . " (id_producto, nombre_producto, cantidad_producto, precio_producto, subtotal) VALUES ('$id_product', '$productName', '$quantityToAdd', '$price_product', '$subtotal')";
    $connection->query($addItem);
} else {
    $response = false;
    $createTable = "CREATE TABLE " . $tableName . "(id_producto INT(6), nombre_producto TEXT(30), cantidad_producto INT(5), precio_producto DOUBLE(10, 2), subtotal DOUBLE(10, 2))";
    $connection->query($createTable);
    $addItem = "INSERT INTO " . $tableName . " (id_producto, nombre_producto, cantidad_producto, precio_producto, subtotal) VALUES ('$id_product', '$productName', '$quantityToAdd', '$price_product', '$subtotal')";
    $connection->query($addItem);
}

// Devolver la respuesta en formato JSON
echo json_encode($response);

// Cierra la conexión a la base de datos
$connection->close();
