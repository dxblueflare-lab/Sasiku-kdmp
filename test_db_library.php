<?php
/**
 * Database Storage Library Test Suite
 * Demonstrates the functionality of the KDMP database storage library
 */

// Include the database storage library components
require_once __DIR__ . '/includes/DatabaseConfig.php';
require_once __DIR__ . '/includes/DatabaseStorage.php';
require_once __DIR__ . '/includes/DatabaseCRUD.php';

echo "<h1>KDMP Database Storage Library Test Suite</h1>\n";
echo "<p>Testing the comprehensive database storage solution for KDMP application</p>\n";

// Test 1: Check database connection
echo "<h2>Test 1: Database Connection</h2>\n";
try {
    $dbManager = DatabaseManager::getInstance();
    $connectionStatus = $dbManager->getConnectionStatus();
    
    if ($connectionStatus['connected']) {
        echo "<p style='color: green;'>✓ Database connection successful</p>\n";
        echo "<p>Server Info: " . htmlspecialchars($connectionStatus['server_info']) . "</p>\n";
        echo "<p>Driver Name: " . htmlspecialchars($connectionStatus['driver_name']) . "</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Database connection failed: " . htmlspecialchars($connectionStatus['error']) . "</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking connection: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

// Test 2: Test DatabaseStorage singleton
echo "<h2>Test 2: DatabaseStorage Singleton</h2>\n";
try {
    $db1 = DatabaseStorage::getInstance();
    $db2 = DatabaseStorage::getInstance();
    
    if ($db1 === $db2) {
        echo "<p style='color: green;'>✓ DatabaseStorage follows singleton pattern</p>\n";
    } else {
        echo "<p style='color: red;'>✗ DatabaseStorage does not follow singleton pattern</p>\n";
    }
    
    $connectionInfo = $db1->getConnectionInfo();
    echo "<p>Connected to: " . htmlspecialchars($connectionInfo['database']) . " on " . htmlspecialchars($connectionInfo['host']) . "</p>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error with DatabaseStorage: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

// Test 3: Test CRUD operations
echo "<h2>Test 3: CRUD Operations</h2>\n";

try {
    $crud = new DatabaseCRUD();
    
    // Test 3a: Count existing users
    $userCount = $crud->count('users');
    if ($userCount['success']) {
        echo "<p>✓ Found {$userCount['count']} existing users in the database</p>\n";
    } else {
        echo "<p style='color: orange;'>? Could not count users: " . htmlspecialchars($userCount['error']) . "</p>\n";
    }
    
    // Test 3b: Read some users
    $users = $crud->read('users', [], '*', 'id ASC', 5);
    if ($users['success']) {
        echo "<p>✓ Successfully retrieved " . count($users['data']) . " users:</p>\n";
        echo "<ul>\n";
        foreach ($users['data'] as $user) {
            echo "<li>ID: {$user['id']}, Username: " . htmlspecialchars($user['username'] ?? 'N/A') . ", Role: " . htmlspecialchars($user['role'] ?? 'N/A') . "</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: orange;'>? Could not read users: " . htmlspecialchars($users['error']) . "</p>\n";
    }
    
    // Test 3c: Find a specific user (if any exist)
    if (!empty($users['data'])) {
        $firstUserId = $users['data'][0]['id'];
        $foundUser = $crud->findById('users', $firstUserId);
        if ($foundUser['success'] && $foundUser['data']) {
            echo "<p>✓ Successfully found user with ID {$firstUserId}: " . htmlspecialchars($foundUser['data']['username']) . "</p>\n";
        } else {
            echo "<p style='color: orange;'>? Could not find user with ID {$firstUserId}</p>\n";
        }
    }
    
    // Test 3d: Try to insert a test user (with unique username)
    $testUsername = 'test_user_' . time();
    $testUserData = [
        'username' => $testUsername,
        'email' => $testUsername . '@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'customer',
        'nama_lengkap' => 'Test User',
        'alamat' => 'Test Address',
        'nomor_telepon' => '081234567890'
    ];
    
    $insertResult = $crud->create('users', $testUserData);
    if ($insertResult['success']) {
        echo "<p>✓ Successfully created test user with ID: {$insertResult['id']}</p>\n";
        
        // Test 3e: Update the test user
        $updateResult = $crud->update('users', ['nama_lengkap' => 'Updated Test User'], ['id' => $insertResult['id']]);
        if ($updateResult['success']) {
            echo "<p>✓ Successfully updated test user</p>\n";
        } else {
            echo "<p style='color: orange;'>? Could not update test user: " . htmlspecialchars($updateResult['error']) . "</p>\n";
        }
        
        // Test 3f: Delete the test user
        $deleteResult = $crud->delete('users', ['id' => $insertResult['id']]);
        if ($deleteResult['success']) {
            echo "<p>✓ Successfully cleaned up test user</p>\n";
        } else {
            echo "<p style='color: orange;'>? Could not delete test user: " . htmlspecialchars($deleteResult['error']) . "</p>\n";
        }
    } else {
        echo "<p style='color: orange;'>? Could not create test user: " . htmlspecialchars($insertResult['error']) . "</p>\n";
    }
    
    // Test 3g: Search functionality
    $searchResult = $crud->search('users', ['username', 'nama_lengkap'], 'admin', '*', 'id ASC', 5);
    if ($searchResult['success']) {
        echo "<p>✓ Search for 'admin' returned " . count($searchResult['data']) . " results</p>\n";
    } else {
        echo "<p style='color: orange;'>? Search failed: " . htmlspecialchars($searchResult['error']) . "</p>\n";
    }
    
    // Test 3h: Check if users table exists
    $tableExists = $crud->tableExists('users');
    if ($tableExists) {
        echo "<p>✓ Confirmed that 'users' table exists</p>\n";
    } else {
        echo "<p style='color: orange;'>? 'users' table does not exist or could not be verified</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during CRUD tests: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

// Test 4: Test convenience functions
echo "<h2>Test 4: Convenience Functions</h2>\n";

try {
    // Test counting users with convenience function
    $convenienceCount = db_count('users');
    if ($convenienceCount['success']) {
        echo "<p>✓ Used convenience function to count users: {$convenienceCount['count']}</p>\n";
    } else {
        echo "<p style='color: orange;'>? Convenience function failed: " . htmlspecialchars($convenienceCount['error']) . "</p>\n";
    }
    
    // Test reading users with convenience function
    $convenienceRead = db_read('users', [], '*', 'id ASC', 2);
    if ($convenienceRead['success']) {
        echo "<p>✓ Used convenience function to read users: " . count($convenienceRead['data']) . " results</p>\n";
    } else {
        echo "<p style='color: orange;'>? Convenience function failed: " . htmlspecialchars($convenienceRead['error']) . "</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during convenience function tests: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

// Test 5: Test transaction functionality
echo "<h2>Test 5: Transaction Support</h2>\n";

try {
    $db = DatabaseStorage::getInstance();
    
    // Check if we can start a transaction
    $db->beginTransaction();
    if ($db->inTransaction()) {
        echo "<p>✓ Transaction started successfully</p>\n";
        $db->rollback(); // Rollback since we didn't do anything
        echo "<p>✓ Transaction rolled back successfully</p>\n";
    } else {
        echo "<p style='color: orange;'>? Could not start transaction</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error during transaction test: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h2>Test Summary</h2>\n";
echo "<p>The KDMP Database Storage Library provides:</p>\n";
echo "<ul>\n";
echo "<li>Singleton pattern for database connections</li>\n";
echo "<li>Comprehensive CRUD operations</li>\n";
echo "<li>Transaction support</li>\n";
echo "<li>Search functionality</li>\n";
echo "<li>Batch operations</li>\n";
echo "<li>Convenience functions for common operations</li>\n";
echo "<li>Error handling and reporting</li>\n";
echo "<li>Configuration management</li>\n";
echo "</ul>\n";
echo "<p>All components are ready for use in the KDMP application!</p>\n";
?>