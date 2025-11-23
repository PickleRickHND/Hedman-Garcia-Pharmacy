<?php
/**
 * DEPRECATED: This file is no longer needed with the new Shopping_Cart table
 * Redirecting to add_product_bill.php instead
 *
 * This file previously managed dynamic ShoppingCartUser_X tables.
 * Now we use a unified Shopping_Cart table for all users.
 */

// For backward compatibility, redirect to the new endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "add_product_bill.php";
    exit;
}

// If accessed directly, show deprecation message
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'This endpoint is deprecated. Please use add_product_bill.php instead.',
    'deprecated' => true
]);
?>