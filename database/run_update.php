<?php
require_once '../config/config.php';

if (!hasRole('admin')) {
    die('Access denied');
}

try {
    $db = getDBConnection();
    $sql = file_get_contents(__DIR__ . '/update_staff_expenses.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !str_starts_with($statement, '--')) {
            try {
                $db->exec($statement);
            } catch (PDOException $e) {
                // Ignore if column already exists
                if (strpos($e->getMessage(), 'Duplicate column') === false) {
                    echo "Error: " . $e->getMessage() . "<br>";
                }
            }
        }
    }
    
    echo "Database updated successfully!<br>";
    echo "<a href='../dashboard.php'>Go to Dashboard</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
