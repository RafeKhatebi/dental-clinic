<?php
/**
 * Database Configuration
 * Dental Clinic Management System
 */

// Database Type (sqlite or mysql)
define('DB_TYPE', 'mysql');

// SQLite Configuration
define('DB_PATH', __DIR__ . '/../database/dental_clinic.db');

// MySQL Configuration (if using MySQL)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3307);
define('DB_NAME', 'dental_clinic');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get Database Connection
 */
function getDBConnection()
{
    try {
        if (DB_TYPE === 'sqlite') {
            // SQLite Connection
            $db = new PDO('sqlite:' . DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Enable foreign keys for SQLite
            $db->exec('PRAGMA foreign_keys = ON;');

            return $db;
        } else {
            // MySQL Connection
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            return new PDO($dsn, DB_USER, DB_PASS, $options);
        }
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Initialize Database
 */
function initializeDatabase()
{
    if (DB_TYPE === 'sqlite') {
        // Create database directory if not exists
        $dbDir = dirname(DB_PATH);
        if (!file_exists($dbDir)) {
            mkdir($dbDir, 0777, true);
        }

        // Check if database exists
        $dbExists = file_exists(DB_PATH);

        // Get connection
        $db = getDBConnection();

        // If database doesn't exist or is empty, run schema
        if (!$dbExists) {
            $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
            $db->exec($schema);
        }

        return $db;
    } else {
        // For MySQL, just return connection
        // Database should be created manually or via install.php
        return getDBConnection();
    }
}

/**
 * Execute Query
 */
function executeQuery($query, $params = [])
{
    $db = getDBConnection();
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch All Records
 */
function fetchAll($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetchAll();
}

/**
 * Fetch Single Record
 */
function fetchOne($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetch();
}

/**
 * Insert Record
 */
function insertRecord($table, $data)
{
    $db = getDBConnection();
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));

    $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $db->prepare($query);
    $stmt->execute($data);

    return $db->lastInsertId();
}

/**
 * Update Record
 */
function updateRecord($table, $data, $where, $whereParams = [])
{
    $db = getDBConnection();
    $set = [];

    foreach (array_keys($data) as $key) {
        $set[] = "$key = :$key";
    }

    $setString = implode(', ', $set);
    $query = "UPDATE $table SET $setString WHERE $where";

    $params = array_merge($data, $whereParams);
    $stmt = $db->prepare($query);
    $stmt->execute($params);

    return $stmt->rowCount();
}

/**
 * Delete Record
 */
function deleteRecord($table, $where, $params = [])
{
    $db = getDBConnection();
    $query = "DELETE FROM $table WHERE $where";
    $stmt = $db->prepare($query);
    $stmt->execute($params);

    return $stmt->rowCount();
}

// Global database connection for transactions
$GLOBALS['db_connection'] = null;

/**
 * Begin Transaction
 */
function beginTransaction()
{
    $GLOBALS['db_connection'] = getDBConnection();
    return $GLOBALS['db_connection']->beginTransaction();
}

/**
 * Commit Transaction
 */
function commitTransaction()
{
    if ($GLOBALS['db_connection']) {
        $result = $GLOBALS['db_connection']->commit();
        $GLOBALS['db_connection'] = null;
        return $result;
    }
    return false;
}

/**
 * Rollback Transaction
 */
function rollbackTransaction()
{
    if ($GLOBALS['db_connection']) {
        $result = $GLOBALS['db_connection']->rollBack();
        $GLOBALS['db_connection'] = null;
        return $result;
    }
    return false;
}
