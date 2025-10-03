<?php
require_once 'config/config_optimized.php';

if (isLoggedIn()) {
    logActivity('logout', 'users', $_SESSION['user_id'], 'User logged out');
    session_destroy();
}

redirect('/index_optimized.php');
?>