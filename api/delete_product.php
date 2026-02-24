<?php
// api/delete_product.php
// API endpoint to delete a product

require_once __DIR__ . '/../includes/DatabaseConfig.php';
require_once __DIR__ . '/../includes/DatabaseStorage.php';
require_once __DIR__ . '/../includes/DatabaseCRUD.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

$product_id = $_POST['id'] ?? null;

if (!$product_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Product ID is required'
    ]);
    exit();
}

try {
    $crud = new DatabaseCRUD();
    
    // First, check if the product exists
    $existing_product = $crud->findById('produk', $product_id);
    
    if (!$existing_product['success'] || !$existing_product['data']) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        exit();
    }
    
    // Delete the product
    $result = $crud->delete('produk', ['id' => $product_id]);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete product: ' . $result['message']
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting product: ' . $e->getMessage()
    ]);
}
?>