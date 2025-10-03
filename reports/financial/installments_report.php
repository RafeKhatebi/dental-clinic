<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$filter = $_GET['filter'] ?? 'today';

$where = "py.payment_method = 'installment' AND py.status IN ('pending', 'partial')";
if ($filter == 'today') {
    $where .= " AND py.due_date = '" . date('Y-m-d') . "'";
} elseif ($filter == 'week') {
    $where .= " AND py.due_date BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d', strtotime('+7 days')) . "'";
} elseif ($filter == 'overdue') {
    $where .= " AND py.due_date < '" . date('Y-m-d') . "'";
}

$installments = fetchAll("
    SELECT p.first_name, p.last_name, p.patient_code, p.phone,
           py.amount, py.paid_amount, py.due_date, py.installment_number, py.total_installments
    FROM payments py
    JOIN patients p ON py.patient_id = p.id
    WHERE $where
    ORDER BY py.due_date ASC
");

$totalAmount = 0;
foreach ($installments as $i) {
    $totalAmount += ($i['amount'] - $i['paid_amount']);
}

$titles = [
    'today' => 'اقساط امروز',
    'week' => 'اقساط این هفته',
    'overdue' => 'اقساط معوق'
];

$pdf = new SimplePDF();
$pdf->addHeader($titles[$filter] ?? 'گزارش اقساط');
$pdf->addText('<strong>تاریخ:</strong> ' . date('Y/m/d'));
$pdf->addText('<hr>');
$pdf->addText('<h4>خلاصه</h4>');
$pdf->addText('تعداد اقساط: ' . count($installments) . '<br>');
$pdf->addText('<strong>جمع مبلغ: ' . number_format($totalAmount) . ' ریال</strong>');
$pdf->addText('<hr>');

if ($installments) {
    $tableData = [];
    foreach ($installments as $i) {
        $remaining = $i['amount'] - $i['paid_amount'];
        $tableData[] = [
            $i['patient_code'],
            $i['first_name'] . ' ' . $i['last_name'],
            $i['phone'],
            $i['installment_number'] . '/' . $i['total_installments'],
            number_format($remaining),
            $i['due_date']
        ];
    }
    $pdf->addTable(['کد', 'نام', 'تلفن', 'قسط', 'مبلغ', 'سررسید'], $tableData);
}

$pdf->output();
