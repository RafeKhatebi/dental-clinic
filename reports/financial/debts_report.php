<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$debts = fetchAll("
    SELECT p.first_name, p.last_name, p.patient_code, p.phone,
           SUM(py.amount - py.paid_amount) as debt,
           MIN(py.due_date) as due_date
    FROM payments py
    JOIN patients p ON py.patient_id = p.id
    WHERE py.status IN ('pending', 'partial', 'overdue')
    GROUP BY py.patient_id
    HAVING debt > 0
    ORDER BY due_date ASC
");

$totalDebt = 0;
$overdueDebt = 0;
$today = date('Y-m-d');

foreach ($debts as $d) {
    $totalDebt += $d['debt'];
    if ($d['due_date'] < $today) {
        $overdueDebt += $d['debt'];
    }
}

$pdf = new SimplePDF();
$pdf->addHeader('گزارش بدهیها');
$pdf->addText('<strong>تاریخ:</strong> ' . date('Y/m/d'));
$pdf->addText('<hr>');
$pdf->addText('<h4>خلاصه</h4>');
$pdf->addText('تعداد بدهکاران: ' . count($debts) . '<br>');
$pdf->addText('جمع بدهی: ' . number_format($totalDebt) . ' ریال<br>');
$pdf->addText('<strong style="color:red;">بدهی معوق: ' . number_format($overdueDebt) . ' ریال</strong>');
$pdf->addText('<hr>');

if ($debts) {
    $tableData = [];
    foreach ($debts as $d) {
        $status = $d['due_date'] < $today ? '🔴 معوق' : '🟢 جاری';
        $tableData[] = [
            $d['patient_code'],
            $d['first_name'] . ' ' . $d['last_name'],
            $d['phone'],
            number_format($d['debt']),
            $d['due_date'],
            $status
        ];
    }
    $pdf->addTable(['کد', 'نام', 'تلفن', 'بدهی', 'سررسید', 'وضعیت'], $tableData);
}

$pdf->output();
