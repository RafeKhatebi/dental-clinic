<?php
require_once '../../config/config.php';

$paymentId = intval($_GET['id'] ?? 0);
if (!$paymentId) die('Invalid payment ID');

$payment = fetchOne("
    SELECT py.*, p.first_name, p.last_name, p.patient_code
    FROM payments py
    JOIN patients p ON py.patient_id = p.id
    WHERE py.id = ?
", [$paymentId]);

if (!$payment) die('Payment not found');

$receiptNumber = 'RCP-' . str_pad($paymentId, 6, '0', STR_PAD_LEFT);
$lang = loadLanguage();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسید پرداخت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto">
        <div class="no-print flex gap-4 mb-4">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                چاپ
            </button>
            <a href="../../patients/view.php?id=<?php echo $payment['patient_id']; ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                بازگشت
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-6">رسید پرداخت</h1>
            <div class="border-b pb-4 mb-4">
                <p><strong>شماره رسید:</strong> <?php echo $receiptNumber; ?></p>
                <p><strong>تاریخ:</strong> <?php echo $payment['payment_date']; ?></p>
            </div>
            <div class="border-b pb-4 mb-4">
                <p><strong>دریافت شده از:</strong> <?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?></p>
                <p><strong>کد بیمار:</strong> <?php echo $payment['patient_code']; ?></p>
            </div>
            <div class="border-b pb-4 mb-4">
                <p class="text-xl"><strong>مبلغ:</strong> <?php echo number_format($payment['amount']); ?> افغانی</p>
                <p><strong>نوع پرداخت:</strong> <?php echo $payment['payment_method'] == 'cash' ? 'نقدی' : ($payment['payment_method'] == 'installment' ? 'اقساطی' : 'قرضی'); ?></p>
            </div>
            <div class="mt-8 text-center">
                <p>امضا و مهر</p>
            </div>
        </div>
    </div>
</body>
</html>
