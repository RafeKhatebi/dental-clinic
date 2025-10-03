<?php
define('SYSTEM_INIT', true);

class DatabaseConfig {
    private const DB_HOST = '127.0.0.1';
    private const DB_PORT = 3307;
    private const DB_NAME = 'dental_clinic';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = 'mysql:host=' . self::DB_HOST . ';port=' . self::DB_PORT . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';
                self::$connection = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 10
                ]);
            } catch (PDOException $e) {
                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$connection;
    }
    
    public static function createDatabase() {
        try {
            $dsn = 'mysql:host=' . self::DB_HOST . ';port=' . self::DB_PORT . ';charset=utf8mb4';
            $pdo = new PDO($dsn, self::DB_USER, self::DB_PASS);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . self::DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            return true;
        } catch (PDOException $e) {
            throw new Exception('Database creation failed: ' . $e->getMessage());
        }
    }
}

function getDBConnection() {
    return DatabaseConfig::getConnection();
}

function executeQuery($query, $params = []) {
    $db = getDBConnection();
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

function fetchAll($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt->fetchAll();
}

function fetchOne($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt->fetch();
}

function insertRecord($table, $data) {
    $db = getDBConnection();
    $columns = array_keys($data);
    $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
    
    $query = sprintf(
        "INSERT INTO `%s` (`%s`) VALUES (%s)",
        $table,
        implode('`, `', $columns),
        implode(', ', $placeholders)
    );
    
    $stmt = $db->prepare($query);
    $stmt->execute($data);
    return $db->lastInsertId();
}

function updateRecord($table, $data, $where, $whereParams = []) {
    $db = getDBConnection();
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
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt->rowCount();
}

function deleteRecord($table, $where, $params = []) {
    $db = getDBConnection();
    $query = sprintf("DELETE FROM `%s` WHERE %s", $table, $where);
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt->rowCount();
}
?>