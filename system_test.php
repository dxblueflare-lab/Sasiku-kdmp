<?php
/**
 * System Integration Test Script
 * Tests the complete KDMP application with database integration
 */

require_once __DIR__ . '/includes/DatabaseConfig.php';
require_once __DIR__ . '/includes/DatabaseStorage.php';
require_once __DIR__ . '/includes/DatabaseCRUD.php';

echo "<h1>System Integration Test - KDMP Application</h1>\n";
echo "<p>This script tests the complete integration of the KDMP application with the database.</p>\n";

$test_results = [];

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>\n";
try {
    $dbManager = DatabaseManager::getInstance();
    $connectionStatus = $dbManager->getConnectionStatus();
    
    if ($connectionStatus['connected']) {
        echo "<p style='color: green;'>✓ Database connection successful</p>\n";
        $test_results['database_connection'] = true;
    } else {
        echo "<p style='color: red;'>✗ Database connection failed: " . htmlspecialchars($connectionStatus['error']) . "</p>\n";
        $test_results['database_connection'] = false;
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking database connection: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    $test_results['database_connection'] = false;
}

// Test 2: Database CRUD Operations
echo "<h2>Test 2: Database CRUD Operations</h2>\n";
try {
    $crud = new DatabaseCRUD();
    
    // Test 2a: Count existing users
    $userCount = $crud->count('users');
    if ($userCount['success']) {
        echo "<p>✓ Successfully counted users: {$userCount['count']} users in database</p>\n";
        $test_results['count_users'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to count users: " . htmlspecialchars($userCount['error']) . "</p>\n";
        $test_results['count_users'] = false;
    }
    
    // Test 2b: Read some users
    $users = $crud->read('users', [], '*', 'id ASC', 3);
    if ($users['success']) {
        echo "<p>✓ Successfully read " . count($users['data']) . " users from database</p>\n";
        $test_results['read_users'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to read users: " . htmlspecialchars($users['error']) . "</p>\n";
        $test_results['read_users'] = false;
    }
    
    // Test 2c: Insert a test user
    $test_username = 'test_user_' . time();
    $testUserData = [
        'username' => $test_username,
        'email' => $test_username . '@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'customer',
        'nama_lengkap' => 'Test User',
        'alamat' => 'Test Address',
        'nomor_telepon' => '081234567890'
    ];
    
    $insertResult = $crud->create('users', $testUserData);
    if ($insertResult['success']) {
        echo "<p>✓ Successfully created test user with ID: {$insertResult['id']}</p>\n";
        $test_results['create_user'] = true;
        
        // Test 2d: Update the test user
        $updateResult = $crud->update('users', ['nama_lengkap' => 'Updated Test User'], ['id' => $insertResult['id']]);
        if ($updateResult) {
            echo "<p>✓ Successfully updated test user</p>\n";
            $test_results['update_user'] = true;
        } else {
            echo "<p style='color: orange;'>? Could not update test user: " . htmlspecialchars($updateResult['message'] ?? 'Unknown error') . "</p>\n";
            $test_results['update_user'] = false;
        }
        
        // Test 2e: Delete the test user
        $deleteResult = $crud->delete('users', ['id' => $insertResult['id']]);
        if ($deleteResult) {
            echo "<p>✓ Successfully cleaned up test user</p>\n";
            $test_results['delete_user'] = true;
        } else {
            echo "<p style='color: orange;'>? Could not delete test user: " . htmlspecialchars($deleteResult['message'] ?? 'Unknown error') . "</p>\n";
            $test_results['delete_user'] = false;
        }
    } else {
        echo "<p style='color: red;'>✗ Failed to create test user: " . htmlspecialchars($insertResult['message']) . "</p>\n";
        $test_results['create_user'] = false;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during CRUD tests: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    $test_results['crud_operations'] = false;
}

// Test 3: Product Operations
echo "<h2>Test 3: Product Operations</h2>\n";
try {
    // Count products
    $productCount = $crud->count('produk');
    if ($productCount['success']) {
        echo "<p>✓ Successfully counted products: {$productCount['count']} products in database</p>\n";
        $test_results['count_products'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to count products: " . htmlspecialchars($productCount['error']) . "</p>\n";
        $test_results['count_products'] = false;
    }
    
    // Read some products
    $products = $crud->read('produk', [], '*', 'id DESC', 3);
    if ($products['success']) {
        echo "<p>✓ Successfully read " . count($products['data']) . " products from database</p>\n";
        $test_results['read_products'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to read products: " . htmlspecialchars($products['error']) . "</p>\n";
        $test_results['read_products'] = false;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during product tests: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    $test_results['product_operations'] = false;
}

// Test 4: Order Operations
echo "<h2>Test 4: Order Operations</h2>\n";
try {
    // Count orders
    $orderCount = $crud->count('pesanan');
    if ($orderCount['success']) {
        echo "<p>✓ Successfully counted orders: {$orderCount['count']} orders in database</p>\n";
        $test_results['count_orders'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to count orders: " . htmlspecialchars($orderCount['error']) . "</p>\n";
        $test_results['count_orders'] = false;
    }
    
    // Read some orders
    $orders = $crud->read('pesanan', [], '*', 'id DESC', 3);
    if ($orders['success']) {
        echo "<p>✓ Successfully read " . count($orders['data']) . " orders from database</p>\n";
        $test_results['read_orders'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to read orders: " . htmlspecialchars($orders['error']) . "</p>\n";
        $test_results['read_orders'] = false;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during order tests: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    $test_results['order_operations'] = false;
}

// Test 5: Category Operations
echo "<h2>Test 5: Category Operations</h2>\n";
try {
    // Count categories
    $categoryCount = $crud->count('kategori_produk');
    if ($categoryCount['success']) {
        echo "<p>✓ Successfully counted categories: {$categoryCount['count']} categories in database</p>\n";
        $test_results['count_categories'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to count categories: " . htmlspecialchars($categoryCount['error']) . "</p>\n";
        $test_results['count_categories'] = false;
    }
    
    // Read categories
    $categories = $crud->read('kategori_produk', [], '*', 'id ASC');
    if ($categories['success']) {
        echo "<p>✓ Successfully read " . count($categories['data']) . " categories from database</p>\n";
        $test_results['read_categories'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to read categories: " . htmlspecialchars($categories['error']) . "</p>\n";
        $test_results['read_categories'] = false;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during category tests: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    $test_results['category_operations'] = false;
}

// Test 6: Session Handling
echo "<h2>Test 6: Session Handling</h2>\n";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Test session write/read
    $_SESSION['test_key'] = 'test_value_' . time();
    $testValue = $_SESSION['test_key'] ?? null;
    
    if ($testValue) {
        echo "<p>✓ Successfully wrote and read session data</p>\n";
        $test_results['session_handling'] = true;
    } else {
        echo "<p style='color: red;'>✗ Failed to write/read session data</p>\n";
        $test_results['session_handling'] = false;
    }
    
    // Clean up session
    unset($_SESSION['test_key']);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during session tests: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    $test_results['session_handling'] = false;
}

// Test 7: File Structure Verification
echo "<h2>Test 7: File Structure Verification</h2>\n";
$required_files = [
    'index.php',
    'shop.php',
    'auth/login.php',
    'auth/register.php',
    'auth/logout.php',
    'dashboard/admin/index.php',
    'dashboard/customer/index.php',
    'dashboard/supplier/index.php',
    'customer/orders.php',
    'customer/order_detail.php',
    'dashboard/admin/products.php',
    'dashboard/supplier/products.php',
    'dashboard/admin/orders.php',
    'includes/DatabaseConfig.php',
    'includes/DatabaseStorage.php',
    'includes/DatabaseCRUD.php',
    'api/get_product.php'
];

$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        $missing_files[] = $file;
    }
}

if (empty($missing_files)) {
    echo "<p>✓ All required files are present in the system</p>\n";
    $test_results['file_structure'] = true;
} else {
    echo "<p style='color: red;'>✗ Missing files: " . implode(', ', $missing_files) . "</p>\n";
    $test_results['file_structure'] = false;
}

// Summary
echo "<h2>Test Summary</h2>\n";
$passed_tests = array_filter($test_results);
$failed_tests = array_diff_key($test_results, $passed_tests);

echo "<p><strong>Passed Tests: " . count($passed_tests) . "/" . count($test_results) . "</strong></p>\n";

if (empty($failed_tests)) {
    echo "<p style='color: green; font-size: 1.2em; font-weight: bold;'>✓ All tests passed! The KDMP system is fully integrated and functional.</p>\n";
    echo "<p>You can now access the system at <a href='./index.php'>./index.php</a></p>\n";
} else {
    echo "<p style='color: orange; font-size: 1.2em; font-weight: bold;'>⚠ Some tests failed. Please review the issues above.</p>\n";
    echo "<p>Failed tests: " . implode(', ', array_keys($failed_tests)) . "</p>\n";
}

echo "<h3>Detailed Results:</h3>\n<ul>\n";
foreach ($test_results as $test => $result) {
    $status = $result ? '✓' : '✗';
    $color = $result ? 'green' : 'red';
    echo "<li style='color: {$color};'>{$status} {$test}</li>\n";
}
echo "</ul>\n";

echo "<p><strong>Note:</strong> Make sure your web server is configured to run PHP files and has access to a MySQL database with the KDMP schema.</p>\n";
?>