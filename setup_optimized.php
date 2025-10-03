<?php
require_once 'config/database_3307.php';

try {
    echo "<h2>Setting up optimized system...</h2>";
    
    // Create database
    DatabaseConfig::createDatabase();
    echo "✅ Database created<br>";
    
    // Execute optimized schema
    $db = DatabaseConfig::getConnection();
    $schemaPath = __DIR__ . '/database/schema_optimized_fixed.sql';
    
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found");
    }
    
    $schema = file_get_contents($schemaPath);
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
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
    
    echo "✅ Schema executed<br>";
    
    // Fix admin password
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$newHash]);
    
    echo "✅ Admin password fixed<br>";
    
    echo "<br><h3>✅ Setup Complete!</h3>";
    echo "<p><strong>Login:</strong> admin / admin123</p>";
    echo "<a href='index_optimized.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Start Using System</a>";
    
} catch (Exception $e) {
    echo "<h3>❌ Setup Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>