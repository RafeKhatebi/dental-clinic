<?php
require_once 'config/config.php';

try {
    // Check current admin user
    $user = fetchOne("SELECT * FROM users WHERE username = 'admin'");
    
    if ($user) {
        echo "Current admin user found:<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Password hash: " . $user['password'] . "<br>";
        echo "Is active: " . $user['is_active'] . "<br><br>";
        
        // Test password verification
        $testPassword = 'admin123';
        if (password_verify($testPassword, $user['password'])) {
            echo "✅ Password 'admin123' is correct<br>";
        } else {
            echo "❌ Password 'admin123' is incorrect<br>";
            echo "Fixing password...<br>";
            
            // Update with correct password hash
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            updateRecord('users', 
                ['password' => $newHash], 
                'username = ?', 
                ['admin']
            );
            
            echo "✅ Password updated successfully<br>";
            echo "New hash: " . $newHash . "<br>";
        }
    } else {
        echo "❌ Admin user not found. Creating...<br>";
        
        // Create admin user
        $adminData = [
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name' => 'مدیر سیستم',
            'role' => 'admin',
            'is_active' => 1
        ];
        
        $adminId = insertRecord('users', $adminData);
        echo "✅ Admin user created with ID: " . $adminId . "<br>";
    }
    
    echo "<br><a href='index.php' style='background:green;color:white;padding:10px;text-decoration:none;'>Test Login</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>