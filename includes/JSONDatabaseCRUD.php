<?php
/**
 * JSONDatabaseCRUD Class
 * Implements CRUD operations using JSON file storage as fallback
 * Designed for local storage in the KDMP application when SQLite is unavailable
 */

require_once __DIR__ . '/JSONDatabaseStorage.php';

class JSONDatabaseCRUD {
    private $db;

    public function __construct() {
        $this->db = JSONDatabaseStorage::getInstance();
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
    public function read($table, $conditions = [], $fields = '*', $orderBy = null, $limit = null, $offset = null) {
        try {
            $results = $this->db->select($table, $conditions, $fields, $orderBy, $limit, $offset);
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
            $ids = [];
            foreach ($records as $record) {
                $id = $this->db->insert($table, $record);
                if ($id) {
                    $ids[] = $id;
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to insert one or more records',
                        'partial_ids' => $ids
                    ];
                }
            }

            return [
                'success' => true,
                'ids' => $ids,
                'message' => count($ids) . ' records inserted successfully'
            ];
        } catch (Exception $e) {
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
            $result = $this->db->update($table, $updates, $conditions);

            return [
                'success' => $result,
                'message' => $result ? 'Records updated successfully' : 'No records were updated'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Bulk update failed'
            ];
        }
    }

    /**
     * Search records with LIKE conditions (simulated)
     */
    public function search($table, $searchFields, $searchTerm, $fields = '*', $orderBy = null, $limit = null) {
        try {
            if (!is_array($searchFields)) {
                $searchFields = [$searchFields];
            }

            // For JSON storage, we'll do a simple contains search
            $conditions = [];
            $allRecords = $this->db->select($table, [], '*');
            
            $results = array_filter($allRecords['data'] ?? $allRecords, function($record) use ($searchFields, $searchTerm) {
                foreach ($searchFields as $field) {
                    if (isset($record[$field]) && stripos((string)$record[$field], $searchTerm) !== false) {
                        return true;
                    }
                }
                return false;
            });

            // Apply ordering
            if ($orderBy) {
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
            }

            // Apply limit
            if ($limit) {
                $results = array_slice($results, 0, $limit);
            }

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
            $result = $this->db->getTableSchema($table);

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
            return $this->db->tableExists($table);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all tables in the database
     */
    public function getAllTables() {
        try {
            return $this->db->getAllTables();
        } catch (Exception $e) {
            return [];
        }
    }
}

/**
 * Convenience functions for common JSON database operations
 */

/**
 * Create a new record in JSON database
 */
function json_create($table, $data) {
    $crud = new JSONDatabaseCRUD();
    return $crud->create($table, $data);
}

/**
 * Read records from JSON database with optional conditions
 */
function json_read($table, $conditions = [], $fields = '*', $orderBy = null, $limit = null, $offset = null) {
    $crud = new JSONDatabaseCRUD();
    return $crud->read($table, $conditions, $fields, $orderBy, $limit, $offset);
}

/**
 * Find a record by ID in JSON database
 */
function json_find($table, $id, $primaryKey = 'id') {
    $crud = new JSONDatabaseCRUD();
    return $crud->findById($table, $id, $primaryKey);
}

/**
 * Update a record in JSON database
 */
function json_update($table, $data, $conditions = []) {
    $crud = new JSONDatabaseCRUD();
    return $crud->update($table, $data, $conditions);
}

/**
 * Delete a record from JSON database
 */
function json_delete($table, $conditions = []) {
    $crud = new JSONDatabaseCRUD();
    return $crud->delete($table, $conditions);
}

/**
 * Count records in JSON database
 */
function json_count($table, $conditions = []) {
    $crud = new JSONDatabaseCRUD();
    return $crud->count($table, $conditions);
}

/**
 * Search records in JSON database
 */
function json_search($table, $searchFields, $searchTerm, $fields = '*', $orderBy = null, $limit = null) {
    $crud = new JSONDatabaseCRUD();
    return $crud->search($table, $searchFields, $searchTerm, $fields, $orderBy, $limit);
}

/**
 * Check if a table exists in JSON database
 */
function json_table_exists($table) {
    $crud = new JSONDatabaseCRUD();
    return $crud->tableExists($table);
}

/**
 * Get all tables in JSON database
 */
function json_get_all_tables() {
    $crud = new JSONDatabaseCRUD();
    return $crud->getAllTables();
}

/**
 * Get table schema from JSON database
 */
function json_get_table_schema($table) {
    $crud = new JSONDatabaseCRUD();
    return $crud->getTableSchema($table);
}
?>