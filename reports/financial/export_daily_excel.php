<?php
require_once '../../config/config.php';
require_once '../../lib/excel_helper.php';

$date = $_GET['date'] ?? date('Y-m-d');

$services = fetchAll("
    SELECT s.service_name, p.first_name, p.last_name, s.quantity, s.unit_price, s.final_price
    FROM services s
    JOIN patients p ON s.patient_id = p.id
    WHERE s.service_date = ? AND s.status = 'completed'
", [$date]);

$excel = new ExcelHelper();
$excel->setHeaders(['خدمت', 'بیمار', 'تعداد', 'قیمت واحد', 'جمع']);

foreach ($services as $s) {
    $excel->addRow([
        $s['service_name'],
        $s['first_name'] . ' ' . $s['last_name'],
        $s['quantity'],
        $s['unit_price'],
        $s['final_price']
    ]);
}

$excel->export('daily_report_' . $date);
