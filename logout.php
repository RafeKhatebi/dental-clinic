<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    logActivity('logout', 'users', $_SESSION['user_id'], 'User logged out');
    session_destroy();
}

redirect('/index.php');
?>