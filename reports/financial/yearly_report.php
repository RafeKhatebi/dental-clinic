<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$year = $_GET['year'] ?? date('Y');

$monthlyData = [];
for ($m = 1; $m <= 12; $m++) {
    $month = sprintf('%s-%02d', $year, $m);
    $startDate = $month . '-01';
    $endDate = date('Y-m-t', strtotime($startDate));
    
    $income = fetchOne("SELECT SUM(amount) as total FROM payments WHERE payment_date BETWEEN ? AND ?", [$startDate, $endDate])['total'] ?? 0;
    $monthlyData[] = ['month' => $m, 'income' => $income];
}

$totalIncome = array_sum(array_column($monthlyData, 'income'));
$avgIncome = $totalIncome / 12;

$pdf = new SimplePDF();
$pdf->addHeader('گزارش سالانه');
$pdf->addText('<strong>سال:</strong> ' . $year);
$pdf->addText('<hr><h4>خلاصه</h4>');
$pdf->addText('درآمد کل سال: ' . number_format($totalIncome) . ' ریال<br>');
$pdf->addText('میانگین ماهانه: ' . number_format($avgIncome) . ' ریال');
$pdf->addText('<hr><h4>درآمد ماهانه</h4>');

$tableData = [];
$months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
foreach ($monthlyData as $d) {
    $tableData[] = [$months[$d['month']-1], number_format($d['income'])];
}
$pdf->addTable(['ماه', 'درآمد'], $tableData);

$pdf->output();
