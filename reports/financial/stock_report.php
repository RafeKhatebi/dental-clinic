<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$type = $_GET['type'] ?? 'low'; // low or expiring

if ($type == 'low') {
    $medicines = fetchAll("SELECT * FROM medicines WHERE is_active = 1 AND stock_quantity <= min_stock_level ORDER BY stock_quantity ASC");
    $title = 'گزارش داروهای با موجودی کم';
} else {
    $medicines = fetchAll("SELECT * FROM medicines WHERE is_active = 1 AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY expiry_date ASC");
    $title = 'گزارش داروهای رو به انقضا';
}

$pdf = new SimplePDF();
$pdf->addHeader($title);
$pdf->addText('<strong>تاریخ:</strong> ' . date('Y/m/d'));
$pdf->addText('<strong>تعداد:</strong> ' . count($medicines));
$pdf->addText('<hr>');

if ($medicines) {
    $tableData = [];
    foreach ($medicines as $m) {
        if ($type == 'low') {
            $tableData[] = [
                $m['medicine_code'],
                $m['medicine_name'],
                $m['stock_quantity'],
                $m['min_stock_level'],
                number_format($m['purchase_price'])
            ];
        } else {
            $daysLeft = floor((strtotime($m['expiry_date']) - time()) / 86400);
            $tableData[] = [
                $m['medicine_code'],
                $m['medicine_name'],
                $m['stock_quantity'],
                $m['expiry_date'],
                $daysLeft . ' روز'
            ];
        }
    }
    
    if ($type == 'low') {
        $pdf->addTable(['کد', 'نام دارو', 'موجودی', 'حداقل', 'قیمت خرید'], $tableData);
    } else {
        $pdf->addTable(['کد', 'نام دارو', 'موجودی', 'تاریخ انقضا', 'باقیمانده'], $tableData);
    }
} else {
    $pdf->addText('<p style="text-align:center;">موردی یافت نشد</p>');
}

$pdf->output();
