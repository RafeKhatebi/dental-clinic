<?php
require_once 'config/database_3307.php';

try {
    // Test connection
    echo "Testing connection to MySQL on port 3307...<br>";
    
    // Create database first
    DatabaseConfig::createDatabase();
    echo "✅ Database created/verified<br>";
    
    // Get connection
    $db = DatabaseConfig::getConnection();
    echo "✅ Connected successfully<br>";
    
    // Read and execute schema
    $schemaPath = __DIR__ . '/database/schema_optimized_fixed.sql';
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found: $schemaPath");
    }
    
    $schema = file_get_contents($schemaPath);
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    echo "Executing schema...<br>";
    foreach ($statements as $statement) {
        if (!empty($statement) && strpos($statement, '--') !== 0) {
            try {
                $db->exec($statement);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "Warning: " . $e->getMessage() . "<br>";
                }
            }
        }
    }
    
    echo "✅ Schema executed successfully<br>";
    
    // Test with a simple query
    $result = $db->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "✅ Users table has {$result['count']} records<br>";
    
    echo "<hr>";
    echo "<h2>✅ Installation Complete!</h2>";
    echo "<p>Database is working on port 3307</p>";
    echo "<p><strong>Login:</strong> admin / admin123</p>";
    echo "<a href='index.php' style='background:green;color:white;padding:10px;text-decoration:none;'>Go to System</a>";
    
} catch (Exception $e) {
    echo "<h2>❌ Installation Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL is running on port 3307</li>";
    echo "<li>No firewall blocking the connection</li>";
    echo "<li>MySQL user 'root' has no password</li>";
    echo "</ul>";
}
?>