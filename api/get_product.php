<?php
// api/get_product.php
// API endpoint to get product information

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
    $product = $crud->findById('produk', $product_id);
    
    if ($product['success'] && $product['data']) {
        // Get category name
        $category = $crud->findById('kategori_produk', $product['data']['id_kategori'] ?? 1);
        $category_name = $category['success'] ? $category['data']['nama_kategori'] : 'Umum';

        // There is no supplier field in the produk table based on our check
        $supplier_name = 'TBD'; // To be determined - since there's no supplier field in produk table

        $product_data = [
            'id' => $product['data']['id'],
            'nama_produk' => $product['data']['nama_produk'],
            'deskripsi' => $product['data']['deskripsi'] ?? '',
            'harga' => (float)($product['data']['harga'] ?? 0),
            'stok' => (int)($product['data']['stok'] ?? 0),
            'gambar_produk' => $product['data']['gambar_produk'] ?? $product['data']['gambar_url'] ?? $product['data']['gambar'] ?? '',
            'kategori' => $category_name,
            'supplier' => $supplier_name,
            'kategori_id' => $product['data']['id_kategori'] ?? 1,
            'id_supplier' => null // No supplier field in produk table
        ];

        echo json_encode([
            'success' => true,
            'product' => $product_data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving product: ' . $e->getMessage()
    ]);
}
?>