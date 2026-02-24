<?php
/**
 * Initialize Local Database for KDMP Application
 * This script creates the database using SQLite if available, otherwise JSON storage
 */

// Check if SQLite is available
$sqliteAvailable = in_array('sqlite', PDO::getAvailableDrivers());

if ($sqliteAvailable) {
    require_once __DIR__ . '/includes/SQLiteMigration.php';
    echo "<h1>KDMP Local SQLite Database Initialization</h1>\n";
    
    try {
        $migration = new SQLiteMigration();
        
        echo "<h2>Checking for pending migrations...</h2>\n";
        
        $pendingMigrations = $migration->getPendingMigrations();
        $appliedMigrations = $migration->getAppliedMigrations();
        
        echo "<p>Pending migrations: " . count($pendingMigrations) . "</p>\n";
        echo "<p>Already applied migrations: " . count($appliedMigrations) . "</p>\n";
        
        if (!empty($pendingMigrations)) {
            echo "<h3>Running pending migrations:</h3>\n";
            echo "<ul>\n";
            foreach ($pendingMigrations as $migrationName) {
                echo "<li>{$migrationName}</li>\n";
            }
            echo "</ul>\n";
            
            $appliedCount = $migration->runMigrations();
            echo "<p>Successfully applied {$appliedCount} migrations.</p>\n";
        } else {
            echo "<p>No pending migrations. Database is up to date.</p>\n";
        }
        
        // Show applied migrations
        $appliedMigrations = $migration->getAppliedMigrations();
        if (!empty($appliedMigrations)) {
            echo "<h3>Applied Migrations:</h3>\n";
            echo "<ul>\n";
            foreach ($appliedMigrations as $migration) {
                echo "<li>{$migration['migration_name']} - {$migration['applied_at']}</li>\n";
            }
            echo "</ul>\n";
        }
        
        // Test database connection
        echo "<h2>Testing database connection...</h2>\n";
        $manager = SQLiteDatabaseManager::getInstance();
        $status = $manager->getConnectionStatus();
        
        if ($status['connected']) {
            echo "<p style='color: green;'>✓ Database connection successful!</p>\n";
            echo "<p>Database path: " . htmlspecialchars($status['database_path']) . "</p>\n";
            
            // Test basic operations
            echo "<h2>Testing basic database operations...</h2>\n";
            
            $crud = new SQLiteDatabaseCRUD();
            
            // Test table existence
            $tables = $crud->getAllTables();
            echo "<p>Tables in database: " . count($tables) . "</p>\n";
            
            if (in_array('users', $tables)) {
                $userCount = $crud->count('users');
                if ($userCount['success']) {
                    echo "<p>Users table exists with {$userCount['count']} records.</p>\n";
                }
            }
            
            if (in_array('produk', $tables)) {
                $productCount = $crud->count('produk');
                if ($productCount['success']) {
                    echo "<p>Products table exists with {$productCount['count']} records.</p>\n";
                }
            }
            
            echo "<h2>Database initialization completed successfully!</h2>\n";
            echo "<p>Your local SQLite database is ready to use with the KDMP application.</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Database connection failed: " . htmlspecialchars($status['error']) . "</p>\n";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error during database initialization: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
} else {
    // Use JSON-based storage as fallback
    require_once __DIR__ . '/includes/JSONDatabaseCRUD.php';
    echo "<h1>KDMP Local JSON Database Initialization</h1>\n";
    
    echo "<p style='color: orange;'>SQLite is not available. Using JSON-based storage as fallback.</p>\n";
    
    // Create initial tables with sample data
    $crud = new JSONDatabaseCRUD();
    
    // Create users table with a default admin user
    if (!$crud->tableExists('users')) {
        $adminUser = [
            'username' => 'admin',
            'email' => 'admin@kdmp.local',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'nama_lengkap' => 'Administrator',
            'alamat' => 'Local Server',
            'nomor_telepon' => '000-000-0000'
        ];
        
        $result = $crud->create('users', $adminUser);
        if ($result['success']) {
            echo "<p style='color: green;'>✓ Created users table with admin account</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Failed to create users table: " . htmlspecialchars($result['error']) . "</p>\n";
        }
    } else {
        echo "<p>Users table already exists.</p>\n";
    }
    
    // Create categories table with sample data
    if (!$crud->tableExists('kategori_produk')) {
        $categories = [
            ['nama_kategori' => 'Electronics', 'deskripsi' => 'Electronic devices and accessories'],
            ['nama_kategori' => 'Clothing', 'deskripsi' => 'Apparel and fashion items'],
            ['nama_kategori' => 'Home & Garden', 'deskripsi' => 'Home improvement and garden supplies'],
            ['nama_kategori' => 'Books', 'deskripsi' => 'Educational and recreational books']
        ];
        
        foreach ($categories as $category) {
            $crud->create('kategori_produk', $category);
        }
        echo "<p style='color: green;'>✓ Created kategori_produk table with sample data</p>\n";
    } else {
        echo "<p>Kategori produk table already exists.</p>\n";
    }
    
    // Create products table with sample data
    if (!$crud->tableExists('produk')) {
        $products = [
            [
                'nama_produk' => 'Smartphone X',
                'deskripsi' => 'Latest smartphone with advanced features',
                'harga' => 599.99,
                'stok' => 50,
                'id_kategori' => 1
            ],
            [
                'nama_produk' => 'Cotton T-Shirt',
                'deskripsi' => 'Comfortable cotton t-shirt',
                'harga' => 19.99,
                'stok' => 100,
                'id_kategori' => 2
            ],
            [
                'nama_produk' => 'Garden Spade',
                'deskripsi' => 'Durable garden spade for outdoor work',
                'harga' => 24.99,
                'stok' => 30,
                'id_kategori' => 3
            ]
        ];
        
        foreach ($products as $product) {
            $crud->create('produk', $product);
        }
        echo "<p style='color: green;'>✓ Created produk table with sample data</p>\n";
    } else {
        echo "<p>Produk table already exists.</p>\n";
    }
    
    // Test basic operations
    echo "<h2>Testing basic database operations...</h2>\n";
    
    $userCount = $crud->count('users');
    if ($userCount['success']) {
        echo "<p>Users in database: {$userCount['count']}</p>\n";
    }
    
    $productCount = $crud->count('produk');
    if ($productCount['success']) {
        echo "<p>Products in database: {$productCount['count']}</p>\n";
    }
    
    echo "<h2>Database initialization completed successfully!</h2>\n";
    echo "<p>Your local JSON-based database is ready to use with the KDMP application.</p>\n";
    echo "<p>To use SQLite instead, please enable the PDO SQLite extension in your PHP configuration.</p>\n";
}

echo "<h3>Next Steps:</h3>\n";
echo "<ul>\n";
echo "<li>Add sample data to your database</li>\n";
echo "<li>Integrate the local database with your application</li>\n";
if ($sqliteAvailable) {
    echo "<li>Use the SQLiteDatabaseCRUD class for database operations</li>\n";
} else {
    echo "<li>Use the JSONDatabaseCRUD class for database operations</li>\n";
    echo "<li>Enable SQLite support in PHP for better performance</li>\n";
}
echo "</ul>\n";
?>