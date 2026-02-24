<?php
/**
 * DatabaseStorage Library
 * A comprehensive database storage library for the KDMP application
 * Provides connection management, query building, and CRUD operations
 */

class DatabaseStorage {
    private static $instance = null;
    private $connection;
    private $host;
    private $dbname;
    private $username;
    private $password;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->loadConfig();
        $this->connect();
    }
    
    /**
     * Load database configuration
     */
    private function loadConfig() {
        // Check if config file exists
        if (file_exists(__DIR__ . '/../config/database.php')) {
            require_once __DIR__ . '/../config/database.php';
            $db = new Database();
            $this->host = $db->host;
            $this->dbname = $db->db_name;
            $this->username = $db->username;
            $this->password = $db->password;
        } else {
            // Fallback to constants if config file doesn't exist
            $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $this->dbname = defined('DB_NAME') ? DB_NAME : 'ecommerce_dapur_mbg';
            $this->username = defined('DB_USER') ? DB_USER : 'root';
            $this->password = defined('DB_PASS') ? DB_PASS : '';
        }
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true, // Use persistent connections
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
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
     * Get the PDO connection object
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a raw SQL query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query execution failed: " . $e->getMessage());
        }
    }
    
    /**
     * Fetch all records from a query
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Fetch a single record from a query
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Insert a record into a table
     */
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute($data);
            
            if ($result) {
                return $this->connection->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Insert failed: " . $e->getMessage());
        }
    }
    
    /**
     * Update records in a table
     */
    public function update($table, $data, $conditions = []) {
        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
        $whereClause = '';
        $params = $data;
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $paramName = 'condition_' . $column;
                $whereParts[] = "{$column} = :{$paramName}";
                $params[$paramName] = $value;
            }
            $whereClause = ' WHERE ' . implode(' AND ', $whereParts);
        }
        
        $sql = "UPDATE {$table} SET {$setClause}{$whereClause}";
        
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Update failed: " . $e->getMessage());
        }
    }
    
    /**
     * Delete records from a table
     */
    public function delete($table, $conditions = []) {
        $whereClause = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $paramName = 'condition_' . $column;
                $whereParts[] = "{$column} = :{$paramName}";
                $params[$paramName] = $value;
            }
            $whereClause = ' WHERE ' . implode(' AND ', $whereParts);
        }
        
        $sql = "DELETE FROM {$table}{$whereClause}";
        
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }
    
    /**
     * Select records from a table with conditions
     */
    public function select($table, $conditions = [], $fields = '*', $orderBy = null, $limit = null) {
        $fieldList = is_array($fields) ? implode(', ', $fields) : $fields;
        $sql = "SELECT {$fieldList} FROM {$table}";
        
        $whereClause = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $paramName = 'condition_' . $column;
                $whereParts[] = "{$column} = :{$paramName}";
                $params[$paramName] = $value;
            }
            $whereClause = ' WHERE ' . implode(' AND ', $whereParts);
        }
        
        $sql .= $whereClause;
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->fetchAll($sql, $params);
    }
    
    /**
     * Find a single record by ID
     */
    public function findById($table, $id, $primaryKey = 'id') {
        $sql = "SELECT * FROM {$table} WHERE {$primaryKey} = :id";
        return $this->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Count records in a table with conditions
     */
    public function count($table, $conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $paramName = 'condition_' . $column;
                $whereParts[] = "{$column} = :{$paramName}";
                $params[$paramName] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $whereParts);
        }
        
        $result = $this->fetchOne($sql, $params);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
    
    /**
     * Check if a transaction is active
     */
    public function inTransaction() {
        return $this->connection->inTransaction();
    }
    
    /**
     * Escape a string for safe use in queries (deprecated in favor of prepared statements)
     */
    public function escape($string) {
        return $this->connection->quote($string);
    }
    
    /**
     * Get the last inserted ID
     */
    public function getLastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Get database connection info
     */
    public function getConnectionInfo() {
        return [
            'host' => $this->host,
            'database' => $this->dbname,
            'connected' => $this->connection !== null
        ];
    }
}
?>