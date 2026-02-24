<?php
/**
 * SQLite Migration System for KDMP Application
 * Handles database schema creation and updates for the local SQLite database
 */

require_once __DIR__ . '/SQLiteDatabaseCRUD.php';

class SQLiteMigration {
    private $db;
    private $migrationsDir;
    
    public function __construct() {
        $this->db = SQLiteDatabaseStorage::getInstance();
        $this->migrationsDir = __DIR__ . '/../migrations';
        
        // Ensure migrations directory exists
        if (!is_dir($this->migrationsDir)) {
            mkdir($this->migrationsDir, 0755, true);
        }
        
        // Create migrations table if it doesn't exist
        $this->initMigrationsTable();
    }
    
    /**
     * Initialize the migrations tracking table
     */
    private function initMigrationsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration_name TEXT UNIQUE NOT NULL,
            applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Failed to create migrations table: " . $e->getMessage());
        }
    }
    
    /**
     * Check if a migration has already been applied
     */
    private function isMigrationApplied($migrationName) {
        $sql = "SELECT COUNT(*) as count FROM migrations WHERE migration_name = :migration_name";
        $result = $this->db->fetchOne($sql, ['migration_name' => $migrationName]);
        return $result['count'] > 0;
    }
    
    /**
     * Mark a migration as applied
     */
    private function markMigrationApplied($migrationName) {
        $sql = "INSERT INTO migrations (migration_name) VALUES (:migration_name)";
        try {
            $this->db->query($sql, ['migration_name' => $migrationName]);
            return true;
        } catch (Exception $e) {
            error_log("Failed to mark migration as applied: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Run all pending migrations
     */
    public function runMigrations() {
        $appliedCount = 0;
        
        // Get all migration files
        $migrationFiles = glob($this->migrationsDir . '/*_*.php');
        sort($migrationFiles); // Sort to ensure migrations run in order
        
        foreach ($migrationFiles as $file) {
            $fileName = basename($file);
            $migrationName = pathinfo($fileName, PATHINFO_FILENAME);
            
            if (!$this->isMigrationApplied($migrationName)) {
                echo "Running migration: {$migrationName}\n";
                
                try {
                    require_once $file;
                    
                    // Call the migration function (should be named after the file)
                    $migrationFunction = str_replace(['-', '.', ' '], '_', $migrationName);
                    if (function_exists($migrationFunction)) {
                        $result = $migrationFunction($this->db);
                        
                        if ($result) {
                            $this->markMigrationApplied($migrationName);
                            $appliedCount++;
                            echo "Migration {$migrationName} completed successfully.\n";
                        } else {
                            echo "Migration {$migrationName} failed.\n";
                        }
                    } else {
                        echo "Migration function {$migrationFunction} not found in {$fileName}.\n";
                    }
                } catch (Exception $e) {
                    echo "Error running migration {$migrationName}: " . $e->getMessage() . "\n";
                }
            }
        }
        
        return $appliedCount;
    }
    
    /**
     * Create a new migration file
     */
    public function createMigration($name) {
        $timestamp = date('Ymd_His');
        $fileName = $this->migrationsDir . '/' . $timestamp . '_' . $name . '.php';
        
        $template = "<?php
/**
 * Migration: {$name}
 * Created: " . date('Y-m-d H:i:s') . "
 */

function {$name}(\$db) {
    try {
        // Example migration - create a sample table
        /*
        \$sql = \"
        CREATE TABLE IF NOT EXISTS sample_table (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )\";
        \$db->query(\$sql);
        */
        
        // Add your migration logic here
        // Return true on success, false on failure
        return true;
    } catch (Exception \$e) {
        error_log('Migration failed: ' . \$e->getMessage());
        return false;
    }
}
?>";
        
        file_put_contents($fileName, $template);
        return $fileName;
    }
    
    /**
     * Get list of applied migrations
     */
    public function getAppliedMigrations() {
        $sql = "SELECT * FROM migrations ORDER BY applied_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get list of pending migrations
     */
    public function getPendingMigrations() {
        $appliedMigrations = $this->getAppliedMigrations();
        $appliedNames = array_column($appliedMigrations, 'migration_name');
        
        $allMigrationFiles = glob($this->migrationsDir . '/*_*.php');
        $pendingMigrations = [];
        
        foreach ($allMigrationFiles as $file) {
            $fileName = basename($file);
            $migrationName = pathinfo($fileName, PATHINFO_FILENAME);
            
            if (!in_array($migrationName, $appliedNames)) {
                $pendingMigrations[] = $migrationName;
            }
        }
        
        return $pendingMigrations;
    }
}

/**
 * Migration helper functions
 */

/**
 * Create a table with columns definition
 */
function create_table($db, $tableName, $columns) {
    $columnDefs = [];
    
    foreach ($columns as $name => $definition) {
        $columnDefs[] = "{$name} {$definition}";
    }
    
    $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (" . implode(', ', $columnDefs) . ")";
    $db->query($sql);
}

/**
 * Drop a table
 */
function drop_table($db, $tableName) {
    $sql = "DROP TABLE IF EXISTS {$tableName}";
    $db->query($sql);
}

/**
 * Add a column to a table
 */
function add_column($db, $tableName, $columnName, $definition) {
    $sql = "ALTER TABLE {$tableName} ADD COLUMN {$columnName} {$definition}";
    $db->query($sql);
}

/**
 * Create an index
 */
function create_index($db, $tableName, $columns, $indexName = null) {
    if (!$indexName) {
        $indexName = $tableName . '_' . implode('_', (array)$columns) . '_idx';
    }
    
    $columnList = is_array($columns) ? implode(', ', $columns) : $columns;
    $sql = "CREATE INDEX IF NOT EXISTS {$indexName} ON {$tableName} ({$columnList})";
    $db->query($sql);
}

/**
 * Drop an index
 */
function drop_index($db, $indexName) {
    $sql = "DROP INDEX IF EXISTS {$indexName}";
    $db->query($sql);
}

// Example usage:
// $migration = new SQLiteMigration();
// $migration->runMigrations();
?>