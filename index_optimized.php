<?php
require_once 'config/config_optimized.php';

// Check if already logged in
if (isLoggedIn()) {
    redirect('/dashboard_optimized.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            $user = fetchOne("SELECT * FROM users WHERE username = ? AND is_active = 1", [$username]);
            
            if ($user && verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                logActivity('login', 'users', $user['id'], 'User logged in');
                redirect('/dashboard_optimized.php');
            } else {
                $error = 'نام کاربری یا رمز عبور اشتباه است';
            }
        } catch (Exception $e) {
            $error = 'خطا در اتصال به دیتابیس';
        }
    } else {
        $error = 'لطفاً تمام فیلدها را پر کنید';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود - سیستم مدیریت مرکز دندانپزشکی</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .login-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="login-card p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">سیستم مدیریت مرکز دندانپزشکی</h2>
                            <p class="text-muted">نسخه بهینه شده</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">نام کاربری</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">رمز عبور</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">ورود</button>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                پیشفرض: admin / admin123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>