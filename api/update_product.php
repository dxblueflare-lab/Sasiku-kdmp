<?php
// api/update_product.php
// API endpoint to update a product

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

$input = json_decode(file_get_contents('php://input'), true);

$product_id = $input['id'] ?? null;
$nama_produk = $input['nama_produk'] ?? null;
$harga = $input['harga'] ?? null;
$stok = $input['stok'] ?? null;
$id_kategori = $input['id_kategori'] ?? null;
$id_supplier = $input['id_supplier'] ?? null;
$deskripsi = $input['deskripsi'] ?? '';

if (!$product_id || !$nama_produk || $harga === null || $stok === null || !$id_kategori || !$id_supplier) {
    echo json_encode([
        'success' => false,
        'message' => 'All required fields must be provided'
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
    
    // Prepare update data
    $product_data = [
        'nama_produk' => $nama_produk,
        'deskripsi' => $deskripsi,
        'harga' => $harga,
        'stok' => $stok,
        'id_kategori' => $id_kategori,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Update the product
    $result = $crud->update('produk', $product_data, ['id' => $product_id]);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update product: ' . $result['message']
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating product: ' . $e->getMessage()
    ]);
}
?>