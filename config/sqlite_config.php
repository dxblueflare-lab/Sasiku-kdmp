<?php
/**
 * SQLite Local Database Configuration for KDMP Application
 * Contains SQLite database connection settings and initialization
 */

// Define SQLite database configuration constants
defined('SQLITE_DB_PATH') or define('SQLITE_DB_PATH', __DIR__ . '/../database/local.db');
defined('SQLITE_TIMEOUT') or define('SQLITE_TIMEOUT', 30);

// Define additional SQLite options
defined('SQLITE_CHARSET') or define('SQLITE_CHARSET', 'UTF-8');

// Define connection options
defined('SQLITE_PDO_OPTIONS') or define('SQLITE_PDO_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false, // SQLite doesn't benefit much from persistent connections
    PDO::ATTR_TIMEOUT => SQLITE_TIMEOUT,
]);

/**
 * SQLiteDatabaseManager Class
 * Manages SQLite database connections and configurations
 */
class SQLiteDatabaseManager {
    private static $instance = null;
    private $config;
    private $connections = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->config = [
            'database_path' => SQLITE_DB_PATH,
            'timeout' => SQLITE_TIMEOUT,
            'charset' => SQLITE_CHARSET,
            'options' => SQLITE_PDO_OPTIONS
        ];
        
        // Ensure database directory exists
        $dbDir = dirname(SQLITE_DB_PATH);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get database configuration
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Get a database connection
     */
    public function getConnection($name = 'default') {
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->createConnection();
        }
        return $this->connections[$name];
    }

    /**
     * Create a new database connection
     */
    private function createConnection() {
        try {
            // Ensure the database directory exists
            $dbDir = dirname($this->config['database_path']);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $dsn = "sqlite:{$this->config['database_path']}";
            $pdo = new PDO($dsn, null, null, $this->config['options']);

            // Set additional SQLite pragmas for better performance and reliability
            $pdo->exec("PRAGMA foreign_keys = ON"); // Enable foreign key constraints
            $pdo->exec("PRAGMA journal_mode = WAL"); // Use WAL mode for better concurrency
            $pdo->exec("PRAGMA synchronous = NORMAL"); // Better performance with reasonable durability
            $pdo->exec("PRAGMA cache_size = 1000"); // Size of cache in pages
            $pdo->exec("PRAGMA temp_store = MEMORY"); // Store temporary tables in memory

            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("SQLite database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Test database connection
     */
    public function testConnection() {
        try {
            $connection = $this->getConnection();
            $connection->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get connection status
     */
    public function getConnectionStatus() {
        try {
            $connection = $this->getConnection();
            $connection->query("SELECT 1");
            return [
                'connected' => true,
                'database_path' => $this->config['database_path'],
                'driver_name' => $connection->getAttribute(PDO::ATTR_DRIVER_NAME),
            ];
        } catch (Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Set custom configuration
     */
    public function setConfig($key, $value) {
        $this->config[$key] = $value;
        // Reset connections to use new config
        $this->connections = [];
    }
    
    /**
     * Get database file path
     */
    public function getDatabasePath() {
        return $this->config['database_path'];
    }
}

/**
 * Helper function to get SQLite database connection
 */
function get_sqlite_db_connection($name = 'default') {
    $dbManager = SQLiteDatabaseManager::getInstance();
    return $dbManager->getConnection($name);
}

/**
 * Helper function to get SQLite database configuration
 */
function get_sqlite_db_config() {
    $dbManager = SQLiteDatabaseManager::getInstance();
    return $dbManager->getConfig();
}

/**
 * Initialize SQLite database connection
 */
function initialize_sqlite_database() {
    try {
        $dbManager = SQLiteDatabaseManager::getInstance();
        if (!$dbManager->testConnection()) {
            throw new Exception("Unable to establish SQLite database connection");
        }
        return true;
    } catch (Exception $e) {
        error_log("SQLite database initialization failed: " . $e->getMessage());
        return false;
    }
}

// Initialize SQLite database connection
initialize_sqlite_database();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>