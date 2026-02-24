<?php
// check_database_structure.php
// Script to check the actual database structure

require_once __DIR__ . '/includes/JSONDatabaseCRUD.php';

echo "<h1>Checking Database Structure</h1>\n";

// Check if we're using JSON storage or MySQL
if (class_exists('JSONDatabaseCRUD')) {
    echo "<h2>Using JSON Database Storage</h2>\n";
    
    $crud = new JSONDatabaseCRUD();
    
    // Check if produk table exists
    if ($crud->tableExists('produk')) {
        echo "<p>Produk table exists.</p>\n";
        
        // Get sample data
        $result = $crud->read('produk', [], '*', 'id ASC', 1);
        if ($result['success'] && !empty($result['data'])) {
            $sample = $result['data'][0];
            echo "<h3>Sample Produk Record:</h3>\n";
            echo "<pre>" . print_r($sample, true) . "</pre>\n";
        } else {
            echo "<p>No produk records found.</p>\n";
        }
        
        // Get table schema
        $schema = $crud->getTableSchema('produk');
        if ($schema['success']) {
            echo "<h3>Produk Table Schema:</h3>\n";
            echo "<pre>" . print_r($schema['schema'], true) . "</pre>\n";
        }
    } else {
        echo "<p>Produk table does not exist.</p>\n";
    }
    
    // Check kategori_produk table
    if ($crud->tableExists('kategori_produk')) {
        echo "<h3>Kategori Produk Sample:</h3>\n";
        $result = $crud->read('kategori_produk', [], '*', 'id ASC', 1);
        if ($result['success'] && !empty($result['data'])) {
            $sample = $result['data'][0];
            echo "<pre>" . print_r($sample, true) . "</pre>\n";
        }
    }
    
    // Check users table
    if ($crud->tableExists('users')) {
        echo "<h3>Users Sample:</h3>\n";
        $result = $crud->read('users', [], '*', 'id ASC', 1);
        if ($result['success'] && !empty($result['data'])) {
            $sample = $result['data'][0];
            echo "<pre>" . print_r($sample, true) . "</pre>\n";
        }
    }
} else {
    echo "<p>JSON Database storage not available.</p>\n";
}
?>