<?php
/**
 * Database Configuration for KDMP Application
 * Contains database connection settings and initialization
 */

// Define database configuration constants
defined('DB_HOST') or define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
defined('DB_NAME') or define('DB_NAME', getenv('DB_NAME') ?: 'ecommerce_dapur_mbg');
defined('DB_USER') or define('DB_USER', getenv('DB_USER') ?: 'root');
defined('DB_PASS') or define('DB_PASS', getenv('DB_PASS') ?: '');
defined('DB_PORT') or define('DB_PORT', getenv('DB_PORT') ?: '3306');

// Define additional database options
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8mb4');
defined('DB_COLLATION') or define('DB_COLLATION', 'utf8mb4_unicode_ci');

// Define connection options
defined('PDO_OPTIONS') or define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => true, // Use persistent connections for better performance
]);

/**
 * DatabaseManager Class
 * Manages database connections and configurations
 */
class DatabaseManager {
    private static $instance = null;
    private $config;
    private $connections = [];
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->config = [
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASS,
            'port' => DB_PORT,
            'charset' => DB_CHARSET,
            'collation' => DB_COLLATION,
            'options' => PDO_OPTIONS
        ];
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
            $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']};charset={$this->config['charset']}";
            
            $pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
            
            // Set collation
            $pdo->exec("SET NAMES {$this->config['charset']} COLLATE {$this->config['collation']}");
            
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
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
                'server_info' => $connection->getAttribute(PDO::ATTR_SERVER_INFO),
                'driver_name' => $connection->getAttribute(PDO::ATTR_DRIVER_NAME),
                'client_version' => $connection->getAttribute(PDO::ATTR_CLIENT_VERSION),
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
}

/**
 * Helper function to get database connection
 */
function get_db_connection($name = 'default') {
    $dbManager = DatabaseManager::getInstance();
    return $dbManager->getConnection($name);
}

/**
 * Helper function to get database configuration
 */
function get_db_config() {
    $dbManager = DatabaseManager::getInstance();
    return $dbManager->getConfig();
}

/**
 * Initialize database connection
 */
function initialize_database() {
    try {
        $dbManager = DatabaseManager::getInstance();
        if (!$dbManager->testConnection()) {
            throw new Exception("Unable to establish database connection");
        }
        return true;
    } catch (Exception $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Environment-specific configuration
 */
function load_environment_config() {
    $env_file = __DIR__ . '/../.env';
    
    if (file_exists($env_file)) {
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((startsWith($value, '"') && endsWith($value, '"')) ||
                    (startsWith($value, "'") && endsWith($value, "'"))) {
                    $value = substr($value, 1, -1);
                }
                
                putenv("{$key}={$value}");
            }
        }
    }
}

/**
 * String helper functions
 */
function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
}

// Load environment configuration if available
load_environment_config();

// Initialize database connection
initialize_database();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>