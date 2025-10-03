<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$serviceId = intval($_GET['id'] ?? 0);
if (!$serviceId) die('Invalid service ID');

$service = fetchOne("
    SELECT s.*, p.first_name, p.last_name, p.patient_code, p.phone, u.full_name as dentist_name
    FROM services s
    JOIN patients p ON s.patient_id = p.id
    LEFT JOIN users u ON s.dentist_id = u.id
    WHERE s.id = ?
", [$serviceId]);

if (!$service) die('Service not found');

$invoiceNumber = generateInvoiceNumber('SRV');
$subtotal = $service['total_price'];
$discount = $service['discount'];
$tax = ($subtotal - $discount) * 0.09;
$total = $subtotal - $discount + $tax;

$pdf = new SimplePDF();
$pdf->addHeader('فاکتور خدمات دندانپزشکی');
$pdf->addText('<strong>شماره فاکتور:</strong> ' . $invoiceNumber . ' | <strong>تاریخ:</strong> ' . date('Y/m/d'));
$pdf->addText('<hr>');
$pdf->addText('<strong>بیمار:</strong> ' . $service['first_name'] . ' ' . $service['last_name'] . ' | کد: ' . $service['patient_code']);
$pdf->addText('<hr>');

$pdf->addTable(
    ['ردیف', 'خدمت', 'تعداد', 'قیمت واحد', 'جمع'],
    [['1', $service['service_name'], $service['quantity'], number_format($service['unit_price']), number_format($service['total_price'])]]
);

$pdf->addText('<div style="text-align:left;">جمع: ' . number_format($subtotal) . ' ریال<br>');
$pdf->addText('تخفیف: ' . number_format($discount) . ' ریال<br>');
$pdf->addText('مالیات (9%): ' . number_format($tax) . ' ریال<br>');
$pdf->addText('<strong>جمع کل: ' . number_format($total) . ' ریال</strong><br>');
$pdf->addText('به حروف: ' . numberToWords($total) . ' ریال</div>');

$pdf->output();
