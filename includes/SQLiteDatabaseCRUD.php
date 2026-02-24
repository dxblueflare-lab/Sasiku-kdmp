<?php
/**
 * SQLiteDatabaseCRUD Class
 * Implements comprehensive CRUD operations using the SQLiteDatabaseStorage library
 * Designed for local SQLite database storage in the KDMP application
 */

require_once __DIR__ . '/SQLiteDatabaseStorage.php';

class SQLiteDatabaseCRUD {
    private $db;

    public function __construct() {
        $this->db = SQLiteDatabaseStorage::getInstance();
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

    /**
     * Get the underlying database connection
     */
    public function getConnection() {
        return $this->db->getConnection();
    }
}

/**
 * Convenience functions for common SQLite operations
 */

/**
 * Create a new record in SQLite database
 */
function sqlite_create($table, $data) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->create($table, $data);
}

/**
 * Read records from SQLite database with optional conditions
 */
function sqlite_read($table, $conditions = [], $fields = '*', $orderBy = null, $limit = null, $offset = null) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->read($table, $conditions, $fields, $orderBy, $limit, $offset);
}

/**
 * Find a record by ID in SQLite database
 */
function sqlite_find($table, $id, $primaryKey = 'id') {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->findById($table, $id, $primaryKey);
}

/**
 * Update a record in SQLite database
 */
function sqlite_update($table, $data, $conditions = []) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->update($table, $data, $conditions);
}

/**
 * Delete a record from SQLite database
 */
function sqlite_delete($table, $conditions = []) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->delete($table, $conditions);
}

/**
 * Count records in SQLite database
 */
function sqlite_count($table, $conditions = []) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->count($table, $conditions);
}

/**
 * Execute a raw query in SQLite database
 */
function sqlite_query($sql, $params = []) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->query($sql, $params);
}

/**
 * Search records in SQLite database
 */
function sqlite_search($table, $searchFields, $searchTerm, $fields = '*', $orderBy = null, $limit = null) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->search($table, $searchFields, $searchTerm, $fields, $orderBy, $limit);
}

/**
 * Check if a table exists in SQLite database
 */
function sqlite_table_exists($table) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->tableExists($table);
}

/**
 * Get all tables in SQLite database
 */
function sqlite_get_all_tables() {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->getAllTables();
}

/**
 * Get table schema from SQLite database
 */
function sqlite_get_table_schema($table) {
    $crud = new SQLiteDatabaseCRUD();
    return $crud->getTableSchema($table);
}
?>