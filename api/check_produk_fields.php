<?php
// api/check_produk_fields.php
// API endpoint to check the actual field names in the produk table

require_once __DIR__ . '/../includes/DatabaseConfig.php';
require_once __DIR__ . '/../includes/DatabaseStorage.php';
require_once __DIR__ . '/../includes/DatabaseCRUD.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    $crud = new DatabaseCRUD();
    
    // Get table schema information
    $schema = $crud->getTableSchema('produk');
    
    if ($schema['success']) {
        echo json_encode([
            'success' => true,
            'schema' => $schema['schema']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to get table schema: ' . $schema['message']
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking table schema: ' . $e->getMessage()
    ]);
}
?>