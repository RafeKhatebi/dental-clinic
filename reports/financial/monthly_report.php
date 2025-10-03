<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$month = $_GET['month'] ?? date('Y-m');
$startDate = $month . '-01';
$endDate = date('Y-m-t', strtotime($startDate));

$cashIncome = fetchOne("SELECT SUM(amount) as total FROM payments WHERE payment_date BETWEEN ? AND ? AND payment_method = 'cash'", [$startDate, $endDate])['total'] ?? 0;
$installmentIncome = fetchOne("SELECT SUM(paid_amount) as total FROM payments WHERE paid_date BETWEEN ? AND ? AND payment_method = 'installment'", [$startDate, $endDate])['total'] ?? 0;
$totalIncome = $cashIncome + $installmentIncome;

$patientsCount = fetchOne("SELECT COUNT(DISTINCT patient_id) as total FROM services WHERE service_date BETWEEN ? AND ?", [$startDate, $endDate])['total'] ?? 0;
$servicesCount = fetchOne("SELECT COUNT(*) as total FROM services WHERE service_date BETWEEN ? AND ? AND status = 'completed'", [$startDate, $endDate])['total'] ?? 0;

$topServices = fetchAll("
    SELECT service_name, COUNT(*) as count, SUM(final_price) as total
    FROM services
    WHERE service_date BETWEEN ? AND ? AND status = 'completed'
    GROUP BY service_name
    ORDER BY total DESC
    LIMIT 5
", [$startDate, $endDate]);

$pdf = new SimplePDF();
$pdf->addHeader('گزارش ماهانه');
$pdf->addText('<strong>ماه:</strong> ' . $month);
$pdf->addText('<hr><h4>خلاصه مالی</h4>');
$pdf->addText('دریافتی نقدی: ' . number_format($cashIncome) . ' ریال<br>');
$pdf->addText('دریافتی اقساط: ' . number_format($installmentIncome) . ' ریال<br>');
$pdf->addText('<strong>جمع کل: ' . number_format($totalIncome) . ' ریال</strong>');
$pdf->addText('<hr><h4>آمار</h4>');
$pdf->addText('تعداد بیماران: ' . $patientsCount . '<br>تعداد خدمات: ' . $servicesCount);

if ($topServices) {
    $pdf->addText('<hr><h4>پرفروشترین خدمات</h4>');
    $tableData = [];
    foreach ($topServices as $s) {
        $tableData[] = [$s['service_name'], $s['count'], number_format($s['total'])];
    }
    $pdf->addTable(['خدمت', 'تعداد', 'درآمد'], $tableData);
}

$pdf->output();
