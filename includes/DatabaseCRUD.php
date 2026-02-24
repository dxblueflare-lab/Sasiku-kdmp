<?php
/**
 * DatabaseCRUD Class
 * Implements comprehensive CRUD operations using the DatabaseStorage library
 */

require_once __DIR__ . '/DatabaseStorage.php';

class DatabaseCRUD {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseStorage::getInstance();
    }
    
    /**
     * Create a new record
     */
    public function create($table, $data) {
        try {
            $id = $this->db->insert($table, $data);
            return [
                'success' => true,
                'id' => $id,
                'message' => 'Record created successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to create record'
            ];
        }
    }
    
    /**
     * Read records with optional conditions
     */
    public function read($table, $conditions = [], $fields = '*', $orderBy = null, $limit = null) {
        try {
            $results = $this->db->select($table, $conditions, $fields, $orderBy, $limit);
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'message' => 'Failed to read records'
            ];
        }
    }
    
    /**
     * Find a single record by ID
     */
    public function findById($table, $id, $primaryKey = 'id') {
        try {
            $result = $this->db->findById($table, $id, $primaryKey);
            return [
                'success' => true,
                'data' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => null,
                'message' => 'Failed to find record'
            ];
        }
    }
    
    /**
     * Update a record
     */
    public function update($table, $data, $conditions = []) {
        try {
            $result = $this->db->update($table, $data, $conditions);
            return [
                'success' => $result,
                'message' => $result ? 'Record updated successfully' : 'No records were updated'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to update record'
            ];
        }
    }
    
    /**
     * Delete a record
     */
    public function delete($table, $conditions = []) {
        try {
            $result = $this->db->delete($table, $conditions);
            return [
                'success' => $result,
                'message' => $result ? 'Record deleted successfully' : 'No records were deleted'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to delete record'
            ];
        }
    }
    
    /**
     * Count records with optional conditions
     */
    public function count($table, $conditions = []) {
        try {
            $count = $this->db->count($table, $conditions);
            return [
                'success' => true,
                'count' => $count
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'count' => 0,
                'message' => 'Failed to count records'
            ];
        }
    }
    
    /**
     * Execute a raw query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->query($sql, $params);
            return [
                'success' => true,
                'statement' => $stmt
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to execute query'
            ];
        }
    }
    
    /**
     * Fetch all results from a query
     */
    public function fetchAll($sql, $params = []) {
        try {
            $results = $this->db->fetchAll($sql, $params);
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'message' => 'Failed to fetch records'
            ];
        }
    }
    
    /**
     * Fetch a single result from a query
     */
    public function fetchOne($sql, $params = []) {
        try {
            $result = $this->db->fetchOne($sql, $params);
            return [
                'success' => true,
                'data' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => null,
                'message' => 'Failed to fetch record'
            ];
        }
    }
    
    /**
     * Batch insert multiple records
     */
    public function batchInsert($table, $records) {
        if (empty($records)) {
            return [
                'success' => false,
                'message' => 'No records to insert'
            ];
        }
        
        try {
            $this->db->beginTransaction();
            
            $ids = [];
            foreach ($records as $record) {
                $id = $this->db->insert($table, $record);
                if ($id) {
                    $ids[] = $id;
                } else {
                    $this->db->rollback();
                    return [
                        'success' => false,
                        'message' => 'Failed to insert one or more records',
                        'partial_ids' => $ids
                    ];
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'ids' => $ids,
                'message' => count($ids) . ' records inserted successfully'
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Batch insert failed'
            ];
        }
    }
    
    /**
     * Bulk update records
     */
    public function bulkUpdate($table, $updates, $conditions = []) {
        if (empty($updates)) {
            return [
                'success' => false,
                'message' => 'No updates provided'
            ];
        }
        
        try {
            $this->db->beginTransaction();
            
            $result = $this->db->update($table, $updates, $conditions);
            
            if ($result) {
                $this->db->commit();
                return [
                    'success' => true,
                    'message' => 'Records updated successfully'
                ];
            } else {
                $this->db->rollback();
                return [
                    'success' => false,
                    'message' => 'No records were updated'
                ];
            }
        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Bulk update failed'
            ];
        }
    }
    
    /**
     * Search records with LIKE conditions
     */
    public function search($table, $searchFields, $searchTerm, $fields = '*', $orderBy = null, $limit = null) {
        try {
            if (!is_array($searchFields)) {
                $searchFields = [$searchFields];
            }
            
            $whereClauses = [];
            $params = [];
            
            foreach ($searchFields as $field) {
                $paramName = 'search_' . $field;
                $whereClauses[] = "{$field} LIKE :{$paramName}";
                $params[$paramName] = "%{$searchTerm}%";
            }
            
            $whereClause = '(' . implode(' OR ', $whereClauses) . ')';
            
            $fieldList = is_array($fields) ? implode(', ', $fields) : $fields;
            $sql = "SELECT {$fieldList} FROM {$table} WHERE {$whereClause}";
            
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy}";
            }
            
            if ($limit) {
                $sql .= " LIMIT {$limit}";
            }
            
            $results = $this->db->fetchAll($sql, $params);
            
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'message' => 'Search failed'
            ];
        }
    }
    
    /**
     * Get table schema information
     */
    public function getTableSchema($table) {
        try {
            $sql = "DESCRIBE {$table}";
            $result = $this->db->fetchAll($sql);
            
            return [
                'success' => true,
                'schema' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'schema' => null,
                'message' => 'Failed to get table schema'
            ];
        }
    }
    
    /**
     * Check if a table exists
     */
    public function tableExists($table) {
        try {
            $sql = "SHOW TABLES LIKE :table";
            $result = $this->db->fetchOne($sql, ['table' => $table]);
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get the underlying database connection
     */
    public function getConnection() {
        return $this->db->getConnection();
    }
}

/**
 * Convenience functions for common operations
 */

/**
 * Create a new record
 */
function db_create($table, $data) {
    $crud = new DatabaseCRUD();
    return $crud->create($table, $data);
}

/**
 * Read records with optional conditions
 */
function db_read($table, $conditions = [], $fields = '*', $orderBy = null, $limit = null) {
    $crud = new DatabaseCRUD();
    return $crud->read($table, $conditions, $fields, $orderBy, $limit);
}

/**
 * Find a record by ID
 */
function db_find($table, $id, $primaryKey = 'id') {
    $crud = new DatabaseCRUD();
    return $crud->findById($table, $id, $primaryKey);
}

/**
 * Update a record
 */
function db_update($table, $data, $conditions = []) {
    $crud = new DatabaseCRUD();
    return $crud->update($table, $data, $conditions);
}

/**
 * Delete a record
 */
function db_delete($table, $conditions = []) {
    $crud = new DatabaseCRUD();
    return $crud->delete($table, $conditions);
}

/**
 * Count records
 */
function db_count($table, $conditions = []) {
    $crud = new DatabaseCRUD();
    return $crud->count($table, $conditions);
}

/**
 * Execute a raw query
 */
function db_query($sql, $params = []) {
    $crud = new DatabaseCRUD();
    return $crud->query($sql, $params);
}

/**
 * Search records
 */
function db_search($table, $searchFields, $searchTerm, $fields = '*', $orderBy = null, $limit = null) {
    $crud = new DatabaseCRUD();
    return $crud->search($table, $searchFields, $searchTerm, $fields, $orderBy, $limit);
}
?>