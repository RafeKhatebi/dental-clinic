<?php
require_once 'config/config.php';

try {
    $db = getDBConnection();
    
    // Check current tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Current tables: " . implode(', ', $tables) . "<br><br>";
    
    // Execute optimized schema
    $schemaPath = __DIR__ . '/database/schema_optimized_fixed.sql';
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found");
    }
    
    $schema = file_get_contents($schemaPath);
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    echo "Executing optimized schema...<br>";
    foreach ($statements as $statement) {
        if (!empty($statement) && strpos($statement, '--') !== 0) {
            try {
                $db->exec($statement);
                echo "✅ Executed: " . substr($statement, 0, 50) . "...<br>";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "❌ Error: " . $e->getMessage() . "<br>";
                }
            }
        }
    }
    
    // Fix admin password
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$newHash]);
    
    echo "<br>✅ Schema updated and admin password fixed<br>";
    echo "<a href='index.php' style='background:green;color:white;padding:10px;text-decoration:none;'>Login Now</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>