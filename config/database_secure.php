<?php
/**
 * Secure Database Configuration
 * Dental Clinic Management System - Enhanced Version
 */

// Prevent direct access
if (!defined('SYSTEM_INIT')) {
    http_response_code(403);
    exit('Direct access forbidden');
}

// Database Configuration
class DatabaseConfig {
    // Database settings
    private const DB_TYPE = 'sqlite'; // Changed to SQLite for better security
    private const DB_PATH = __DIR__ . '/../database/dental_clinic.db';
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'dental_clinic';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_CHARSET = 'utf8mb4';
    
    // Connection pool
    private static $connections = [];
    private static $instance = null;
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get secure database connection with proper error handling
     */
    public function getConnection() {
        $connectionId = md5(self::DB_TYPE . self::DB_PATH);
        
        if (!isset(self::$connections[$connectionId])) {
            try {
                if (self::DB_TYPE === 'sqlite') {
                    self::$connections[$connectionId] = $this->createSQLiteConnection();
                } else {
                    self::$connections[$connectionId] = $this->createMySQLConnection();
                }
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection failed. Please check configuration.");
            }
        }
        
        return self::$connections[$connectionId];
    }
    
    /**
     * Create SQLite connection with security settings
     */
    private function createSQLiteConnection() {
        // Ensure database directory exists and is secure
        $dbDir = dirname(self::DB_PATH);
        if (!file_exists($dbDir)) {
            if (!mkdir($dbDir, 0750, true)) {
                throw new Exception("Cannot create database directory");
            }
        }
        
        // Set secure permissions
        chmod($dbDir, 0750);
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 30
        ];
        
        $pdo = new PDO('sqlite:' . self::DB_PATH, null, null, $options);
        
        // Enable security features
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        $pdo->exec('PRAGMA synchronous = NORMAL');
        $pdo->exec('PRAGMA temp_store = MEMORY');
        $pdo->exec('PRAGMA mmap_size = 268435456'); // 256MB
        
        return $pdo;
    }
    
    /**
     * Create MySQL connection with security settings
     */
    private function createMySQLConnection() {
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
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ];
        
        return new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
    }
}

/**
 * Secure Database Operations Class
 */
class SecureDB {
    private $db;
    private static $instance = null;
    
    private function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Execute prepared statement with parameters
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
     * Fetch all records with security validation
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->execute($query, $params);
        $results = $stmt->fetchAll();
        
        // Sanitize output
        return $this->sanitizeResults($results);
    }
    
    /**
     * Fetch single record with security validation
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->execute($query, $params);
        $result = $stmt->fetch();
        
        if ($result) {
            return $this->sanitizeResults([$result])[0];
        }
        
        return false;
    }
    
    /**
     * Insert record with validation
     */
    public function insert($table, $data) {
        // Validate table name
        if (!$this->isValidTableName($table)) {
            throw new Exception("Invalid table name");
        }
        
        // Sanitize data
        $data = $this->sanitizeInput($data);
        
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
        
        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->escapeIdentifier($table),
            implode(', ', array_map([$this, 'escapeIdentifier'], $columns)),
            implode(', ', $placeholders)
        );
        
        $this->execute($query, $data);
        return $this->db->lastInsertId();
    }
    
    /**
     * Update record with validation
     */
    public function update($table, $data, $where, $whereParams = []) {
        // Validate table name
        if (!$this->isValidTableName($table)) {
            throw new Exception("Invalid table name");
        }
        
        // Sanitize data
        $data = $this->sanitizeInput($data);
        $whereParams = $this->sanitizeInput($whereParams);
        
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = $this->escapeIdentifier($column) . ' = :' . $column;
        }
        
        $query = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $this->escapeIdentifier($table),
            implode(', ', $setParts),
            $where
        );
        
        $params = array_merge($data, $whereParams);
        $stmt = $this->execute($query, $params);
        
        return $stmt->rowCount();
    }
    
    /**
     * Delete record with validation
     */
    public function delete($table, $where, $params = []) {
        // Validate table name
        if (!$this->isValidTableName($table)) {
            throw new Exception("Invalid table name");
        }
        
        // Sanitize parameters
        $params = $this->sanitizeInput($params);
        
        $query = sprintf(
            "DELETE FROM %s WHERE %s",
            $this->escapeIdentifier($table),
            $where
        );
        
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
    
    /**
     * Validate table name against whitelist
     */
    private function isValidTableName($table) {
        $allowedTables = [
            'users', 'patients', 'services', 'patient_services', 'payments',
            'installments', 'medicines', 'medicine_sales', 'medicine_sale_items',
            'medicine_stock', 'suppliers', 'purchases', 'purchase_items',
            'partners', 'partner_shares', 'prescriptions', 'prescription_items',
            'settings', 'activity_logs', 'backups'
        ];
        
        return in_array($table, $allowedTables, true);
    }
    
    /**
     * Escape database identifier
     */
    private function escapeIdentifier($identifier) {
        // Remove any non-alphanumeric characters except underscore
        $identifier = preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
        return '`' . $identifier . '`';
    }
    
    /**
     * Sanitize input data
     */
    private function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        if (is_string($data)) {
            // Remove null bytes and control characters
            $data = str_replace("\0", '', $data);
            $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
            return trim($data);
        }
        
        return $data;
    }
    
    /**
     * Sanitize output results
     */
    private function sanitizeResults($results) {
        if (!is_array($results)) {
            return $results;
        }
        
        return array_map(function($row) {
            if (is_array($row)) {
                return array_map(function($value) {
                    if (is_string($value)) {
                        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    }
                    return $value;
                }, $row);
            }
            return $row;
        }, $results);
    }
}

/**
 * Helper functions for backward compatibility
 */
function getDBConnection() {
    return DatabaseConfig::getInstance()->getConnection();
}

function executeQuery($query, $params = []) {
    return SecureDB::getInstance()->execute($query, $params);
}

function fetchAll($query, $params = []) {
    return SecureDB::getInstance()->fetchAll($query, $params);
}

function fetchOne($query, $params = []) {
    return SecureDB::getInstance()->fetchOne($query, $params);
}

function insertRecord($table, $data) {
    return SecureDB::getInstance()->insert($table, $data);
}

function updateRecord($table, $data, $where, $whereParams = []) {
    return SecureDB::getInstance()->update($table, $data, $where, $whereParams);
}

function deleteRecord($table, $where, $params = []) {
    return SecureDB::getInstance()->delete($table, $where, $params);
}

function beginTransaction() {
    return SecureDB::getInstance()->beginTransaction();
}

function commitTransaction() {
    return SecureDB::getInstance()->commit();
}

function rollbackTransaction() {
    return SecureDB::getInstance()->rollback();
}

/**
 * Initialize database with security checks
 */
function initializeSecureDatabase() {
    try {
        $db = DatabaseConfig::getInstance()->getConnection();
        
        // Check if database is initialized
        $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
        
        if (empty($tables)) {
            // Database is empty, initialize it
            $schemaPath = __DIR__ . '/../database/schema_secure.sql';
            if (file_exists($schemaPath)) {
                $schema = file_get_contents($schemaPath);
                $db->exec($schema);
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