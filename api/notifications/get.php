<?php
require_once '../../config/config.php';

if (!isLoggedIn()) errorResponse('Unauthorized', 401);

try {
    $notifications = [];
    
    // بدهیهای معوق
    $overdueDebts = fetchOne("SELECT COUNT(*) as count FROM payments WHERE status = 'overdue' AND due_date < ?", [date('Y-m-d')])['count'];
    if ($overdueDebts > 0) {
        $notifications[] = [
            'type' => 'warning',
            'icon' => '⚠️',
            'title' => 'بدهی معوق',
            'message' => "$overdueDebts بدهی معوق",
            'link' => BASE_URL . '/reports/financial/debts_report.php'
        ];
    }
    
    // داروهای کم موجود
    $lowStock = fetchOne("SELECT COUNT(*) as count FROM medicines WHERE stock_quantity <= min_stock_level AND is_active = 1")['count'];
    if ($lowStock > 0) {
        $notifications[] = [
            'type' => 'error',
            'icon' => '📦',
            'title' => 'موجودی کم',
            'message' => "$lowStock دارو کم موجود",
            'link' => BASE_URL . '/medicines/index.php?low_stock=1'
        ];
    }
    
    // داروهای در حال انقضا
    $expiring = fetchOne("SELECT COUNT(*) as count FROM medicines WHERE expiry_date <= ? AND expiry_date >= ? AND is_active = 1", [date('Y-m-d', strtotime('+30 days')), date('Y-m-d')])['count'];
    if ($expiring > 0) {
        $notifications[] = [
            'type' => 'warning',
            'icon' => '⏰',
            'title' => 'انقضای دارو',
            'message' => "$expiring دارو رو به انقضا",
            'link' => BASE_URL . '/medicines/index.php?expiring=1'
        ];
    }
    
    successResponse('OK', ['notifications' => $notifications, 'count' => count($notifications)]);
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
