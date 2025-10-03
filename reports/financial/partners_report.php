<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$totalIncome = fetchOne("
    SELECT SUM(amount) as total 
    FROM payments 
    WHERE payment_date BETWEEN ? AND ? AND payment_method = 'cash'
", [$startDate, $endDate])['total'] ?? 0;

$partners = fetchAll("
    SELECT d.partner_name, d.share_percentage
    FROM documents d
    WHERE d.document_type = 'partner_share' AND d.status = 'active'
");

$pdf = new SimplePDF();
$pdf->addHeader('گزارش سهم شرکا');
$pdf->addText('<strong>دوره:</strong> ' . $startDate . ' تا ' . $endDate);
$pdf->addText('<hr>');
$pdf->addText('<h4>درآمد کل دوره</h4>');
$pdf->addText('<strong>' . number_format($totalIncome) . ' ریال</strong>');
$pdf->addText('<hr>');

if ($partners) {
    $tableData = [];
    foreach ($partners as $p) {
        $share = ($totalIncome * $p['share_percentage']) / 100;
        $tableData[] = [
            $p['partner_name'],
            $p['share_percentage'] . '%',
            number_format($share)
        ];
    }
    $pdf->addTable(['نام شریک', 'درصد سهم', 'مبلغ سهم'], $tableData);
}

$pdf->output();
