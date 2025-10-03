<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$date = $_GET['date'] ?? date('Y-m-d');

$cashIncome = fetchOne("SELECT SUM(amount) as total FROM payments WHERE payment_date = ? AND payment_method = 'cash'", [$date])['total'] ?? 0;
$installmentIncome = fetchOne("SELECT SUM(paid_amount) as total FROM payments WHERE paid_date = ? AND payment_method = 'installment'", [$date])['total'] ?? 0;
$patientsCount = fetchOne("SELECT COUNT(DISTINCT patient_id) as total FROM services WHERE service_date = ?", [$date])['total'] ?? 0;

$services = fetchAll("
    SELECT s.service_name, COUNT(*) as count, SUM(s.final_price) as total
    FROM services s
    WHERE s.service_date = ? AND s.status = 'completed'
    GROUP BY s.service_name
", [$date]);

$totalIncome = $cashIncome + $installmentIncome;

$pdf = new SimplePDF();
$pdf->addHeader('گزارش روزانه صندوق');
$pdf->addText('<strong>تاریخ:</strong> ' . $date);
$pdf->addText('<hr>');
$pdf->addText('<h4>خلاصه مالی</h4>');
$pdf->addText('دریافتی نقدی: ' . number_format($cashIncome) . ' ریال<br>');
$pdf->addText('دریافتی اقساط: ' . number_format($installmentIncome) . ' ریال<br>');
$pdf->addText('<strong>جمع کل: ' . number_format($totalIncome) . ' ریال</strong>');
$pdf->addText('<hr>');
$pdf->addText('<h4>آمار</h4>');
$pdf->addText('تعداد بیماران: ' . $patientsCount . '<br>تعداد خدمات: ' . count($services));

if ($services) {
    $pdf->addText('<hr><h4>خدمات ارائه شده</h4>');
    $tableData = [];
    foreach ($services as $s) {
        $tableData[] = [$s['service_name'], $s['count'], number_format($s['total'])];
    }
    $pdf->addTable(['خدمت', 'تعداد', 'مبلغ'], $tableData);
}

$pdf->output();
