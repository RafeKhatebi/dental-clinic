<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('/index.php');
}

try {
    // Get statistics using optimized schema
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
        WHERE s.status != 'template' 
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

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">داشبورد</h1>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">بیماران امروز</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $todayPatients; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-injured fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">درآمد امروز</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($todayRevenue); ?> ریال</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">کل بیماران</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalPatients; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">داروهای کم موجود</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $lowStockMedicines; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Services -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخرین خدمات ارائه شده</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
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
                                        <td colspan="6" class="text-center">هیچ خدمتی یافت نشد</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentServices as $service): ?>
                                        <tr>
                                            <td><?php echo $service['first_name'] . ' ' . $service['last_name']; ?></td>
                                            <td><?php echo $service['service_name']; ?></td>
                                            <td><?php echo $service['dentist_name'] ?? 'نامشخص'; ?></td>
                                            <td><?php echo formatDate($service['service_date']); ?></td>
                                            <td><?php echo formatCurrency($service['final_price'] ?? 0); ?> ریال</td>
                                            <td>
                                                <span class="badge badge-<?php echo $service['status'] == 'completed' ? 'success' : 'warning'; ?>">
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

<?php include 'includes/footer.php'; ?>