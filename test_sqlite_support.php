<?php
echo "PHP Version: " . phpversion() . "\n";
echo "Available PDO drivers: ";
print_r(PDO::getAvailableDrivers());

if (in_array('sqlite', PDO::getAvailableDrivers())) {
    echo "\nSQLite support is available!\n";
    
    // Try to create a test database
    try {
        $db = new PDO('sqlite::memory:');
        echo "In-memory SQLite database connection successful!\n";
        
        // Test basic operations
        $db->exec("CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)");
        $stmt = $db->prepare("INSERT INTO test (name) VALUES (?)");
        $stmt->execute(['test_data']);
        
        $result = $db->query("SELECT * FROM test")->fetchAll();
        echo "Test data inserted and retrieved: ";
        print_r($result);
        
    } catch (Exception $e) {
        echo "Error with SQLite: " . $e->getMessage() . "\n";
    }
} else {
    echo "\nSQLite support is NOT available in this PHP installation.\n";
    echo "You need to enable the PDO SQLite extension in your php.ini file.\n";
    echo "Look for the line ';extension=pdo_sqlite' and remove the semicolon.\n";
}
?>