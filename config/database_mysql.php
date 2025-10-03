<?php
/**
 * MySQL Database Configuration
 * Dental Clinic Management System - MySQL Version
 */

// Prevent direct access
if (!defined('SYSTEM_INIT')) {
    http_response_code(403);
    exit('Direct access forbidden');
}

// Database Configuration
class MySQLDatabaseConfig {
    // Database settings
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'dental_clinic';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_CHARSET = 'utf8mb4';
    
    // Connection pool
    private static $connection = null;
    private static $instance = null;
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get secure database connection
     */
    public function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=%s",
                    self::DB_HOST,
                    self::DB_NAME,
                    self::DB_CHARSET
                );
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::DB_CHARSET,
                ];
                
                self::$connection = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
                
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection failed. Please check configuration.");
            }
        }
        
        return self::$connection;
    }
}

/**
 * Secure Database Operations Class for MySQL
 */
class MySQLSecureDB {
    private $db;
    private static $instance = null;
    
    private function __construct() {
        $this->db = MySQLDatabaseConfig::getInstance()->getConnection();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Execute prepared statement
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($params);
            
            if (!$result) {
                throw new Exception("Query execution failed");
            }
            
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query failed: " . $e->getMessage());
            throw new Exception("Database operation failed");
        }
    }
    
    /**
     * Fetch all records
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Fetch single record
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetch();
    }
    
    /**
     * Insert record
     */
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
        
        $query = sprintf(
            "INSERT INTO `%s` (`%s`) VALUES (%s)",
            $table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );
        
        $this->execute($query, $data);
        return $this->db->lastInsertId();
    }
    
    /**
     * Update record
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "`{$column}` = :{$column}";
        }
        
        $query = sprintf(
            "UPDATE `%s` SET %s WHERE %s",
            $table,
            implode(', ', $setParts),
            $where
        );
        
        $params = array_merge($data, $whereParams);
        $stmt = $this->execute($query, $params);
        
        return $stmt->rowCount();
    }
    
    /**
     * Delete record
     */
    public function delete($table, $where, $params = []) {
        $query = sprintf("DELETE FROM `%s` WHERE %s", $table, $where);
        $stmt = $this->execute($query, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->rollBack();
    }
}

// Helper functions for backward compatibility
function getDBConnection() {
    return MySQLDatabaseConfig::getInstance()->getConnection();
}

function executeQuery($query, $params = []) {
    return MySQLSecureDB::getInstance()->execute($query, $params);
}

function fetchAll($query, $params = []) {
    return MySQLSecureDB::getInstance()->fetchAll($query, $params);
}

function fetchOne($query, $params = []) {
    return MySQLSecureDB::getInstance()->fetchOne($query, $params);
}

function insertRecord($table, $data) {
    return MySQLSecureDB::getInstance()->insert($table, $data);
}

function updateRecord($table, $data, $where, $whereParams = []) {
    return MySQLSecureDB::getInstance()->update($table, $data, $where, $whereParams);
}

function deleteRecord($table, $where, $params = []) {
    return MySQLSecureDB::getInstance()->delete($table, $where, $params);
}

function beginTransaction() {
    return MySQLSecureDB::getInstance()->beginTransaction();
}

function commitTransaction() {
    return MySQLSecureDB::getInstance()->commit();
}

function rollbackTransaction() {
    return MySQLSecureDB::getInstance()->rollback();
}

/**
 * Initialize MySQL database
 */
function initializeMySQLDatabase() {
    try {
        $db = MySQLDatabaseConfig::getInstance()->getConnection();
        
        // Check if database is initialized by looking for users table
        $stmt = $db->query("SHOW TABLES LIKE 'users'");
        
        if ($stmt->rowCount() === 0) {
            // Database is empty, initialize it
            $schemaPath = __DIR__ . '/../database/schema_mysql.sql';
            if (file_exists($schemaPath)) {
                $schema = file_get_contents($schemaPath);
                
                // Split by semicolon and execute each statement
                $statements = array_filter(array_map('trim', explode(';', $schema)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement) && strpos($statement, '--') !== 0) {
                        try {
                            $db->exec($statement);
                        } catch (PDOException $e) {
                            // Ignore table already exists errors
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                throw $e;
                            }
                        }
                    }
                }
            } else {
                throw new Exception("Database schema file not found");
            }
        }
        
        return $db;
    } catch (Exception $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        throw new Exception("Database initialization failed");
    }
}