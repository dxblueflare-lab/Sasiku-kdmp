<?php
/**
 * JSON-based Local Database Storage for KDMP Application
 * Fallback solution when SQLite is not available
 */

class JSONDatabaseStorage {
    private static $instance = null;
    private $dataDir;
    private $data = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->dataDir = __DIR__ . '/database/json_data';
        
        // Create data directory if it doesn't exist
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        $this->loadData();
    }

    /**
     * Load data from JSON files
     */
    private function loadData() {
        $files = glob($this->dataDir . '/*.json');
        foreach ($files as $file) {
            $tableName = basename($file, '.json');
            $this->data[$tableName] = json_decode(file_get_contents($file), true) ?: [];
        }
    }

    /**
     * Save data to JSON files
     */
    private function saveData($tableName = null) {
        if ($tableName !== null) {
            $this->saveTable($tableName);
        } else {
            foreach ($this->data as $table => $rows) {
                $this->saveTable($table);
            }
        }
    }

    /**
     * Save a specific table
     */
    private function saveTable($tableName) {
        $filePath = $this->dataDir . '/' . $tableName . '.json';
        file_put_contents($filePath, json_encode($this->data[$tableName], JSON_PRETTY_PRINT));
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
     * Insert a record into a table
     */
    public function insert($table, $data) {
        if (!isset($this->data[$table])) {
            $this->data[$table] = [];
        }

        $data['id'] = $this->getNextId($table);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->data[$table][] = $data;
        $this->saveTable($table);
        
        return $data['id'];
    }

    /**
     * Get next available ID for a table
     */
    private function getNextId($table) {
        if (!isset($this->data[$table]) || empty($this->data[$table])) {
            return 1;
        }
        
        $maxId = 0;
        foreach ($this->data[$table] as $row) {
            if (isset($row['id']) && $row['id'] > $maxId) {
                $maxId = $row['id'];
            }
        }
        
        return $maxId + 1;
    }

    /**
     * Update records in a table
     */
    public function update($table, $data, $conditions = []) {
        if (!isset($this->data[$table])) {
            return false;
        }

        $updated = false;
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        foreach ($this->data[$table] as &$row) {
            if ($this->matchConditions($row, $conditions)) {
                foreach ($data as $key => $value) {
                    $row[$key] = $value;
                }
                $updated = true;
            }
        }
        
        if ($updated) {
            $this->saveTable($table);
        }
        
        return $updated;
    }

    /**
     * Delete records from a table
     */
    public function delete($table, $conditions = []) {
        if (!isset($this->data[$table])) {
            return false;
        }

        $originalCount = count($this->data[$table]);
        
        $this->data[$table] = array_filter(
            $this->data[$table],
            function($row) use ($conditions) {
                return !$this->matchConditions($row, $conditions);
            }
        );
        
        $deleted = $originalCount !== count($this->data[$table]);
        
        if ($deleted) {
            $this->saveTable($table);
        }
        
        return $deleted;
    }

    /**
     * Select records from a table with conditions
     */
    public function select($table, $conditions = [], $fields = '*', $orderBy = null, $limit = null, $offset = null) {
        if (!isset($this->data[$table])) {
            return [];
        }

        $results = $this->data[$table];
        
        // Apply conditions
        if (!empty($conditions)) {
            $results = array_filter($results, function($row) use ($conditions) {
                return $this->matchConditions($row, $conditions);
            });
        }
        
        // Apply fields selection
        if ($fields !== '*' && is_string($fields)) {
            $fieldList = explode(',', $fields);
            $fieldList = array_map('trim', $fieldList);
            
            $results = array_map(function($row) use ($fieldList) {
                $filteredRow = [];
                foreach ($fieldList as $field) {
                    if (isset($row[$field])) {
                        $filteredRow[$field] = $row[$field];
                    }
                }
                return $filteredRow;
            }, $results);
        }
        
        // Apply ordering
        if ($orderBy) {
            $results = $this->applyOrdering($results, $orderBy);
        }
        
        // Apply limit and offset
        if ($limit) {
            $offset = $offset ?: 0;
            $results = array_slice($results, $offset, $limit);
        }
        
        return array_values($results);
    }

    /**
     * Find a single record by ID
     */
    public function findById($table, $id, $primaryKey = 'id') {
        if (!isset($this->data[$table])) {
            return null;
        }

        foreach ($this->data[$table] as $row) {
            if (isset($row[$primaryKey]) && $row[$primaryKey] == $id) {
                return $row;
            }
        }
        
        return null;
    }

    /**
     * Count records in a table with conditions
     */
    public function count($table, $conditions = []) {
        if (!isset($this->data[$table])) {
            return 0;
        }

        if (empty($conditions)) {
            return count($this->data[$table]);
        }
        
        $filtered = array_filter($this->data[$table], function($row) use ($conditions) {
            return $this->matchConditions($row, $conditions);
        });
        
        return count($filtered);
    }

    /**
     * Match conditions against a row
     */
    private function matchConditions($row, $conditions) {
        foreach ($conditions as $field => $value) {
            if (!isset($row[$field]) || $row[$field] != $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Apply ordering to results
     */
    private function applyOrdering($results, $orderBy) {
        $parts = explode(' ', trim($orderBy));
        $field = $parts[0];
        $direction = isset($parts[1]) ? strtoupper($parts[1]) : 'ASC';
        
        usort($results, function($a, $b) use ($field, $direction) {
            $valA = isset($a[$field]) ? $a[$field] : null;
            $valB = isset($b[$field]) ? $b[$field] : null;
            
            // Handle numeric comparison
            if (is_numeric($valA) && is_numeric($valB)) {
                $cmp = $valA <=> $valB;
            } else {
                $cmp = strcasecmp((string)$valA, (string)$valB);
            }
            
            return $direction === 'DESC' ? -$cmp : $cmp;
        });
        
        return $results;
    }
    
    /**
     * Check if a table exists
     */
    public function tableExists($tableName) {
        return isset($this->data[$tableName]) || file_exists($this->dataDir . '/' . $tableName . '.json');
    }
    
    /**
     * Get all table names
     */
    public function getAllTables() {
        return array_keys($this->data);
    }
    
    /**
     * Get table schema information (simulated)
     */
    public function getTableSchema($tableName) {
        if (!isset($this->data[$tableName]) || empty($this->data[$tableName])) {
            return [];
        }
        
        $sampleRow = $this->data[$tableName][0];
        $schema = [];
        
        foreach ($sampleRow as $field => $value) {
            $type = gettype($value);
            $schema[] = [
                'name' => $field,
                'type' => $type,
                'nullable' => $value === null
            ];
        }
        
        return $schema;
    }
}
?>