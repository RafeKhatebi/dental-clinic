<?php
// Fix database connection issues
define('SYSTEM_INIT', true);

// Check MySQL service
function checkMySQLService() {
    $output = [];
    exec('sc query mysql', $output);
    return strpos(implode(' ', $output), 'RUNNING') !== false;
}

// Test connection with different configurations
function testConnection($host, $port = 3307) {
    try {
        $dsn = "mysql:host=$host;port=$port";
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Create database if not exists
function createDatabase($host = 'localhost', $port = 3307) {
    try {
        $pdo = new PDO("mysql:host=$host;port=$port", 'root', '');
        $pdo->exec("CREATE DATABASE IF NOT EXISTS dental_clinic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Auto-fix connection
$hosts = ['localhost', '127.0.0.1'];
$ports = [3307, 3307];

echo "<h2>MySQL Connection Diagnostics</h2>";

// Check if MySQL service is running
if (checkMySQLService()) {
    echo "✅ MySQL service is running<br>";
} else {
    echo "❌ MySQL service not running. Start XAMPP MySQL service<br>";
}

// Test different connection combinations
foreach ($hosts as $host) {
    foreach ($ports as $port) {
        if (testConnection($host, $port)) {
            echo "✅ Connection successful: $host:$port<br>";
            
            // Create database
            if (createDatabase($host, $port)) {
                echo "✅ Database 'dental_clinic' created/verified<br>";
                
                // Update config file
                $config = "<?php
define('SYSTEM_INIT', true);

class DatabaseConfig {
    private const DB_HOST = '$host';
    private const DB_PORT = $port;
    private const DB_NAME = 'dental_clinic';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    
    public static function getConnection() {
        try {
            \$dsn = 'mysql:host=' . self::DB_HOST . ';port=' . self::DB_PORT . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';
            return new PDO(\$dsn, self::DB_USER, self::DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException \$e) {
            throw new Exception('Database connection failed: ' . \$e->getMessage());
        }
    }
}

function getDBConnection() {
    return DatabaseConfig::getConnection();
}
?>";
                
                file_put_contents(__DIR__ . '/database_working.php', $config);
                echo "✅ Working config saved to database_working.php<br>";
                echo "<a href='install_mysql.php' style='background:green;color:white;padding:10px;text-decoration:none;'>Install Database</a>";
                exit;
            }
        } else {
            echo "❌ Connection failed: $host:$port<br>";
        }
    }
}

echo "<hr><h3>Manual Steps:</h3>";
echo "1. Start XAMPP Control Panel<br>";
echo "2. Start MySQL service<br>";
echo "3. Check if port 3307 is free<br>";
echo "4. Try again<br>";
?>