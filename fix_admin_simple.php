<?php
require_once 'config/config.php';

try {
    $db = getDBConnection();
    
    // Update admin password directly
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$newHash]);
    
    echo "✅ Admin password updated successfully<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br><a href='index.php' style='background:green;color:white;padding:10px;text-decoration:none;'>Login Now</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>