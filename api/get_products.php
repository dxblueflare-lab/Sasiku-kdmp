<?php
// api/get_products.php
// API endpoint to get all products

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
    
    // Get all products with related information
    $products_result = $crud->read('produk', [], '*', 'id DESC');
    
    if ($products_result['success']) {
        $products = $products_result['data'];
        
        // Get related categories and suppliers for each product
        $enhanced_products = [];
        foreach ($products as $product) {
            // Get category name
            $category_result = $crud->findById('kategori_produk', $product['id_kategori'] ?? 1);
            $category_name = $category_result['success'] ? $category_result['data']['nama_kategori'] : 'Tidak Diketahui';
            
            // There is no supplier field in the produk table based on our check
            // So we'll use a default supplier or leave it blank
            $supplier_name = 'TBD'; // To be determined - since there's no supplier field in produk table
            
            $enhanced_product = [
                'id' => $product['id'],
                'id_formatted' => '#' . sprintf('%03d', $product['id']),
                'nama_produk' => $product['nama_produk'],
                'deskripsi' => $product['deskripsi'] ?? '',
                'harga' => (float)($product['harga'] ?? 0),
                'stok' => (int)($product['stok'] ?? 0),
                'gambar_produk' => $product['gambar_produk'] ?? $product['gambar_url'] ?? $product['gambar'] ?? '',
                'kategori' => $category_name,
                'supplier' => $supplier_name,
                'status' => $product['status'] ?? $product['status_produk'] ?? 'aktif',
                'created_at' => $product['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $product['updated_at'] ?? date('Y-m-d H:i:s')
            ];
            
            $enhanced_products[] = $enhanced_product;
        }
        
        echo json_encode([
            'success' => true,
            'products' => $enhanced_products,
            'count' => count($enhanced_products)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to retrieve products: ' . $products_result['message']
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving products: ' . $e->getMessage()
    ]);
}
?>