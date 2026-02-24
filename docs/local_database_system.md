# KDMP Local SQLite Database System Documentation

## Overview
The KDMP Local SQLite Database System provides a lightweight, file-based database solution for local development and small-scale deployments. This system offers an alternative to the traditional MySQL database and can be used independently or alongside the existing database system.

## Features
- SQLite-based local storage
- Full CRUD operations
- Migration system for schema management
- Compatible with existing KDMP application structure
- Transaction support
- Prepared statements for security

## File Structure
```
kdmp/
├── config/
│   └── sqlite_config.php          # SQLite configuration
├── includes/
│   ├── SQLiteDatabaseStorage.php  # Core database storage class
│   ├── SQLiteDatabaseCRUD.php     # CRUD operations
│   └── SQLiteMigration.php        # Migration system
├── migrations/                    # Migration files
│   └── 20260213103000_create_initial_kdmp_tables.php
├── database/                      # SQLite database file location
│   └── local.db                   # Default database file
└── init_local_db.php             # Database initialization script
```

## Getting Started

### 1. Initialize the Database
Run the initialization script to create the database and apply migrations:
```bash
php init_local_db.php
```

Or visit the script via web browser if you have a web server running.

### 2. Using the Database in Your Application

#### Basic CRUD Operations
```php
// Include the necessary files
require_once 'includes/SQLiteDatabaseCRUD.php';

// Create a CRUD instance
$crud = new SQLiteDatabaseCRUD();

// Create a new record
$result = $crud->create('users', [
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT),
    'role' => 'customer',
    'nama_lengkap' => 'John Doe'
]);

if ($result['success']) {
    echo "User created with ID: " . $result['id'];
}

// Read records
$users = $crud->read('users', ['role' => 'customer'], '*', 'created_at DESC', 10);

// Update a record
$updateResult = $crud->update('users', 
    ['nama_lengkap' => 'John Smith'], 
    ['id' => $userId]
);

// Delete a record
$deleteResult = $crud->delete('users', ['id' => $userId]);
```

#### Using Convenience Functions
```php
// Create a record using convenience function
$result = sqlite_create('users', [
    'username' => 'janedoe',
    'email' => 'jane@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT)
]);

// Read records
$users = sqlite_read('users', ['role' => 'customer']);

// Find a specific record
$user = sqlite_find('users', $userId);

// Count records
$count = sqlite_count('users', ['role' => 'customer']);

// Search records
$results = sqlite_search('users', ['username', 'email'], 'john', '*', 'id DESC', 5);
```

### 3. Creating Custom Migrations

To create a new migration:
```php
$migration = new SQLiteMigration();
$migration->createMigration('add_supplier_table');
```

This will create a new migration file in the `migrations/` directory with a timestamp prefix.

Example migration file content:
```php
<?php
/**
 * Migration: add_supplier_table
 * Created: 2026-02-13 10:30:00
 */

function add_supplier_table($db) {
    try {
        $sql = "
        CREATE TABLE IF NOT EXISTS suppliers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_name TEXT NOT NULL,
            contact_person TEXT,
            phone TEXT,
            email TEXT,
            address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->query($sql);
        
        // Create indexes
        $db->query("CREATE INDEX IF NOT EXISTS idx_suppliers_email ON suppliers(email)");
        
        return true;
    } catch (Exception $e) {
        error_log('Migration failed: ' . $e->getMessage());
        return false;
    }
}
?>
```

After creating your migration, run the initialization script again to apply it:
```bash
php init_local_db.php
```

## Configuration

The SQLite database configuration is located in `config/sqlite_config.php`. You can customize the database path and other settings:

```php
// Change the database file location
defined('SQLITE_DB_PATH') or define('SQLITE_DB_PATH', __DIR__ . '/../database/my_custom.db');
```

## Database Schema

The system creates the following tables by default:

- `users` - User accounts and profiles
- `kategori_produk` - Product categories
- `produk` - Products for sale
- `pesanan` - Customer orders
- `detail_pesanan` - Order items
- `keranjang` - Shopping cart
- `pelacakan_pesanan` - Order tracking
- `ulasan_produk` - Product reviews
- `promosi` - Promotional codes
- `transaksi` - Payment transactions
- `migrations` - Applied migrations tracking

## Best Practices

1. **Always use prepared statements** - The system handles this automatically through the CRUD methods
2. **Use transactions for related operations**:
   ```php
   $db = SQLiteDatabaseStorage::getInstance();
   $db->beginTransaction();
   try {
       // Perform multiple related operations
       $orderId = $crud->create('pesanan', $orderData)['id'];
       $crud->create('detail_pesanan', ['id_pesanan' => $orderId] + $itemData);
       
       $db->commit();
   } catch (Exception $e) {
       $db->rollback();
       throw $e;
   }
   ```
3. **Validate input data** before passing to database operations
4. **Handle errors appropriately** using the success/error indicators in return values

## Troubleshooting

### Common Issues

1. **Permission denied errors**: Ensure the web server has write permissions to the database directory
2. **Database locked errors**: These are usually temporary; SQLite handles concurrent access well but very high loads might cause issues
3. **Migration not applying**: Check that the migration function name matches the filename

### Checking Database Status
```php
$manager = SQLiteDatabaseManager::getInstance();
$status = $manager->getConnectionStatus();
if ($status['connected']) {
    echo "Database connected successfully";
} else {
    echo "Connection failed: " . $status['error'];
}
```

## Integration with Existing Application

The SQLite system is designed to work alongside the existing MySQL system. You can gradually migrate parts of your application to use the local database while keeping others on MySQL.

To switch between databases, simply use the appropriate CRUD class:
- `$mysqlCrud = new DatabaseCRUD();` - For MySQL operations
- `$sqliteCrud = new SQLiteDatabaseCRUD();` - For SQLite operations

## Performance Notes

- SQLite is optimized for read-heavy workloads
- For write-heavy applications, consider staying with MySQL
- SQLite performs well for single-user or low-concurrency scenarios
- The database is stored as a single file, making backups simple

## Security Considerations

- The database file should be stored outside the web root when possible
- Use strong passwords and validate all inputs
- The system uses prepared statements to prevent SQL injection
- Regular backups are recommended