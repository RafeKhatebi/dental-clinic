<?php
require_once 'config/config_optimized.php';

if (!isLoggedIn()) {
    redirect('/index_optimized.php');
}

try {
    // Statistics using optimized schema
    $todayPatients = fetchOne("SELECT COUNT(DISTINCT patient_id) as count FROM services WHERE service_date = CURDATE() AND status != 'template'")['count'] ?? 0;
    $todayRevenue = fetchOne("SELECT SUM(amount) as total FROM payments WHERE payment_date = CURDATE()")['total'] ?? 0;
    $totalPatients = fetchOne("SELECT COUNT(*) as count FROM patients")['count'] ?? 0;
    $lowStockMedicines = fetchOne("SELECT COUNT(*) as count FROM medicines WHERE stock_quantity <= min_stock_level AND is_active = 1")['count'] ?? 0;
    $pendingPayments = fetchOne("SELECT COUNT(*) as count FROM payments WHERE status = 'pending'")['count'] ?? 0;
    
    $recentServices = fetchAll("
        SELECT s.*, p.first_name, p.last_name, u.full_name as dentist_name 
        FROM services s 
        LEFT JOIN patients p ON s.patient_id = p.id 
        LEFT JOIN users u ON s.dentist_id = u.id 
        WHERE s.status != 'template' AND s.patient_id IS NOT NULL
        ORDER BY s.service_date DESC 
        LIMIT 10
    ");
    
} catch (Exception $e) {
    $todayPatients = 0;
    $todayRevenue = 0;
    $totalPatients = 0;
    $lowStockMedicines = 0;
    $pendingPayments = 0;
    $recentServices = [];
}

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد - سیستم مدیریت مرکز دندانپزشکی</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; }
        .stat-card { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 2rem; opacity: 0.3; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">سیستم مدیریت مرکز دندانپزشکی</a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo $currentUser['full_name']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="patients_optimized.php"><i class="fas fa-users"></i> بیماران</a></li>
                        <li><a class="dropdown-item" href="services_optimized.php"><i class="fas fa-tooth"></i> خدمات</a></li>
                        <li><a class="dropdown-item" href="medicines_optimized.php"><i class="fas fa-pills"></i> داروخانه</a></li>
                        <li><a class="dropdown-item" href="payments_optimized.php"><i class="fas fa-money-bill"></i> پرداختها</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> خروج</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">داشبورد</h2>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">بیماران امروز</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo $todayPatients; ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-user-injured"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">درآمد امروز</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo formatCurrency($todayRevenue); ?> ریال</div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">کل بیماران</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo $totalPatients; ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1">داروهای کم موجود</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo $lowStockMedicines; ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-pills"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Services -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">آخرین خدمات ارائه شده</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>بیمار</th>
                                        <th>خدمت</th>
                                        <th>دندانپزشک</th>
                                        <th>تاریخ</th>
                                        <th>مبلغ</th>
                                        <th>وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentServices)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">هیچ خدمتی یافت نشد</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentServices as $service): ?>
                                            <tr>
                                                <td><?php echo ($service['first_name'] ?? '') . ' ' . ($service['last_name'] ?? ''); ?></td>
                                                <td><?php echo $service['service_name']; ?></td>
                                                <td><?php echo $service['dentist_name'] ?? 'نامشخص'; ?></td>
                                                <td><?php echo formatDate($service['service_date']); ?></td>
                                                <td><?php echo formatCurrency($service['final_price'] ?? 0); ?> ریال</td>
                                                <td>
                                                    <span class="badge bg-<?php echo $service['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                                        <?php echo $service['status'] == 'completed' ? 'تکمیل شده' : 'در انتظار'; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>