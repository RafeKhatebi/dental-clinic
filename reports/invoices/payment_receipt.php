<?php
require_once '../../config/config.php';
require_once '../../lib/pdf_helper.php';

$paymentId = intval($_GET['id'] ?? 0);
if (!$paymentId) die('Invalid payment ID');

$payment = fetchOne("
    SELECT py.*, p.first_name, p.last_name, p.patient_code
    FROM payments py
    JOIN patients p ON py.patient_id = p.id
    WHERE py.id = ?
", [$paymentId]);

if (!$payment) die('Payment not found');

$receiptNumber = generateInvoiceNumber('RCP');

$pdf = new SimplePDF();
$pdf->addHeader('رسید پرداخت');
$pdf->addText('<strong>شماره رسید:</strong> ' . $receiptNumber . ' | <strong>تاریخ:</strong> ' . $payment['payment_date']);
$pdf->addText('<hr>');
$pdf->addText('<strong>دریافت شده از:</strong> ' . $payment['first_name'] . ' ' . $payment['last_name']);
$pdf->addText('<strong>کد بیمار:</strong> ' . $payment['patient_code']);
$pdf->addText('<hr>');
$pdf->addText('<strong>مبلغ:</strong> ' . number_format($payment['amount']) . ' ریال');
$pdf->addText('<strong>به حروف:</strong> ' . numberToWords($payment['amount']) . ' ریال');
$pdf->addText('<strong>نوع پرداخت:</strong> ' . ($payment['payment_method'] == 'cash' ? 'نقدی' : ($payment['payment_method'] == 'installment' ? 'اقساطی' : 'قرضی')));
$pdf->addText('<hr>');
$pdf->addText('<p style="text-align:center;">امضا و مهر</p>');

$pdf->output();
