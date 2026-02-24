# KDMP Local Database Quick Start Guide

## Setting Up Local SQLite Database

### Step 1: Initialize the Database
Run the initialization script to create the database and tables:

```bash
php init_local_db.php
```

Or access it via your web browser:
```
http://localhost/kdmp/init_local_db.php
```

### Step 2: Verify Installation
Check that the database file was created:
- Path: `kdmp/database/local.db`
- Size: Should be greater than 0 bytes after initialization

### Step 3: Test Database Connection
```php
<?php
require_once 'includes/SQLiteDatabaseCRUD.php';

// Test connection
$crud = new SQLiteDatabaseCRUD();
$connection = $crud->getConnection();

// Test basic operation
$result = $crud->count('users');
if ($result['success']) {
    echo "Database connection successful!";
    echo "Users in database: " . $result['count'];
} else {
    echo "Database connection failed: " . $result['error'];
}
?>
```

## Basic Usage Examples

### Adding a New User
```php
<?php
require_once 'includes/SQLiteDatabaseCRUD.php';

$crud = new SQLiteDatabaseCRUD();

$userData = [
    'username' => 'newuser',
    'email' => 'newuser@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT),
    'role' => 'customer',
    'nama_lengkap' => 'New User'
];

$result = $crud->create('users', $userData);

if ($result['success']) {
    echo "User created with ID: " . $result['id'];
} else {
    echo "Error: " . $result['error'];
}
?>
```

### Reading Data
```php
<?php
require_once 'includes/SQLiteDatabaseCRUD.php';

$crud = new SQLiteDatabaseCRUD();

// Get all customers
$customers = $crud->read('users', ['role' => 'customer'], '*', 'created_at DESC', 10);

foreach ($customers['data'] as $user) {
    echo "User: " . $user['nama_lengkap'] . " (" . $user['username'] . ")\n";
}

// Find specific user
$user = $crud->findById('users', 1);
if ($user) {
    echo "Found user: " . $user['nama_lengkap'];
}
?>
```

### Updating Records
```php
<?php
require_once 'includes/SQLiteDatabaseCRUD.php';

$crud = new SQLiteDatabaseCRUD();

$updateResult = $crud->update('users', 
    ['nama_lengkap' => 'Updated Name'], 
    ['id' => 1]
);

if ($updateResult['success']) {
    echo "User updated successfully";
} else {
    echo "Update failed: " . $updateResult['error'];
}
?>
```

### Deleting Records
```php
<?php
require_once 'includes/SQLiteDatabaseCRUD.php';

$crud = new SQLiteDatabaseCRUD();

$deleteResult = $crud->delete('users', ['id' => 1]);

if ($deleteResult['success']) {
    echo "User deleted successfully";
} else {
    echo "Delete failed: " . $deleteResult['error'];
}
?>
```

## Adding Sample Data

To populate your database with sample data, create a script like this:

```php
<?php
require_once 'includes/SQLiteDatabaseCRUD.php';

$crud = new SQLiteDatabaseCRUD();

// Add sample categories
$categories = [
    ['nama_kategori' => 'Electronics', 'deskripsi' => 'Electronic devices and accessories'],
    ['nama_kategori' => 'Clothing', 'deskripsi' => 'Apparel and fashion items'],
    ['nama_kategori' => 'Home & Garden', 'deskripsi' => 'Home improvement and garden supplies']
];

foreach ($categories as $category) {
    $crud->create('kategori_produk', $category);
}

// Add sample products
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
    ]
];

foreach ($products as $product) {
    $crud->create('produk', $product);
}

echo "Sample data added successfully!";
?>
```

## Running Migrations

If you need to update your database schema later:

1. Create a new migration file in the `migrations/` directory
2. Run the initialization script again:
   ```bash
   php init_local_db.php
   ```

## Switching Between MySQL and SQLite

To use SQLite instead of MySQL in your application:

**MySQL version:**
```php
require_once 'includes/DatabaseCRUD.php';
$crud = new DatabaseCRUD();
```

**SQLite version:**
```php
require_once 'includes/SQLiteDatabaseCRUD.php';
$crud = new SQLiteDatabaseCRUD();
```

The interface is identical, so you can easily switch between the two systems.

## Troubleshooting

### Common Issues:

1. **"Database file not found"**: Make sure the `database/` directory exists and is writable
2. **"Permission denied"**: Check that your web server has write permissions to the database directory
3. **"Database is locked"**: This is usually temporary; SQLite handles concurrent access automatically

### Checking Database Status:
```php
<?php
require_once 'includes/SQLiteDatabaseCRUD.php';

$manager = SQLiteDatabaseManager::getInstance();
$status = $manager->getConnectionStatus();

if ($status['connected']) {
    echo "Database is accessible at: " . $status['database_path'];
} else {
    echo "Database connection failed: " . $status['error'];
}
?>
```