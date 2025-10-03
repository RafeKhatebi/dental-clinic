<?php
require_once '../config/config.php';

$type = $_GET['type'] ?? 'service'; // service or medicine
$id = intval($_GET['id'] ?? 0);

if (empty($id)) {
    redirect('/dashboard.php');
}

if ($type === 'service') {
    // Get service invoice
    $invoice = fetchOne("
        SELECT ps.*, 
               p.patient_code, p.first_name, p.last_name, p.phone,
               s.service_name,
               u.full_name as dentist_name
        FROM patient_services ps
        JOIN patients p ON ps.patient_id = p.id
        JOIN services s ON ps.service_id = s.id
        JOIN users u ON ps.dentist_id = u.id
        WHERE ps.id = ?
    ", [$id]);
    
    if (!$invoice) {
        redirect('/dashboard.php');
    }
    
    // Get payments for this service
    $payments = fetchAll("
        SELECT * FROM payments 
        WHERE patient_service_id = ?
        ORDER BY payment_date
    ", [$id]);
    
} else {
    // Get medicine sale invoice
    $invoice = fetchOne("
        SELECT ms.*,
               CASE WHEN ms.patient_id IS NOT NULL THEN CONCAT(p.first_name, ' ', p.last_name) ELSE ms.customer_name END as customer_name,
               CASE WHEN ms.patient_id IS NOT NULL THEN p.phone ELSE '' END as phone
        FROM medicine_sales ms
        LEFT JOIN patients p ON ms.patient_id = p.id
        WHERE ms.id = ?
    ", [$id]);
    
    if (!$invoice) {
        redirect('/dashboard.php');
    }
    
    // Get sale items
    $items = fetchAll("
        SELECT msi.*, m.medicine_name, m.unit
        FROM medicine_sale_items msi
        JOIN medicines m ON msi.medicine_id = m.id
        WHERE msi.sale_id = ?
    ", [$id]);
}

// Get clinic settings
$clinicName = getSetting('clinic_name_fa', 'مرکز دندانپزشکی');
$clinicAddress = getSetting('clinic_address', '');
$clinicPhone = getSetting('clinic_phone', '');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتور - <?php echo $type === 'service' ? $invoice['service_name'] : $invoice['sale_code']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold"><?php echo $clinicName; ?></h1>
                    <?php if ($clinicAddress): ?>
                    <p class="text-sm mt-2"><?php echo $clinicAddress; ?></p>
                    <?php endif; ?>
                    <?php if ($clinicPhone): ?>
                    <p class="text-sm">تلفن: <?php echo $clinicPhone; ?></p>
                    <?php endif; ?>
                </div>
                <div class="text-left">
                    <h2 class="text-xl font-bold">فاکتور</h2>
                    <p class="text-sm">تاریخ: <?php echo date('Y-m-d'); ?></p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="p-6 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold mb-3">مشخصات <?php echo $type === 'service' ? 'بیمار' : 'مشتری'; ?></h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">نام</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($type === 'service' ? $invoice['first_name'] . ' ' . $invoice['last_name'] : $invoice['customer_name']); ?></p>
                </div>
                <?php if ($type === 'service'): ?>
                <div>
                    <p class="text-sm text-gray-600">کد بیمار</p>
                    <p class="font-semibold"><?php echo $invoice['patient_code']; ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['phone'])): ?>
                <div>
                    <p class="text-sm text-gray-600">تلفن</p>
                    <p class="font-semibold"><?php echo $invoice['phone']; ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="p-6">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ردیف</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">شرح</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">تعداد</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">قیمت واحد</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">جمع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($type === 'service'): ?>
                    <tr>
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">
                            <?php echo htmlspecialchars($invoice['service_name']); ?>
                            <?php if ($invoice['tooth_number']): ?>
                            <br><span class="text-sm text-gray-600">دندان شماره: <?php echo $invoice['tooth_number']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3"><?php echo $invoice['quantity']; ?></td>
                        <td class="px-4 py-3"><?php echo formatCurrency($invoice['unit_price']); ?></td>
                        <td class="px-4 py-3 font-semibold"><?php echo formatCurrency($invoice['total_price']); ?></td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td class="px-4 py-3"><?php echo $index + 1; ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($item['medicine_name']); ?></td>
                            <td class="px-4 py-3"><?php echo $item['quantity'] . ' ' . $item['unit']; ?></td>
                            <td class="px-4 py-3"><?php echo formatCurrency($item['unit_price']); ?></td>
                            <td class="px-4 py-3 font-semibold"><?php echo formatCurrency($item['total_price']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="p-6 bg-gray-50 border-t">
            <div class="flex justify-end">
                <div class="w-64 space-y-2">
                    <?php if ($type === 'service'): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600">جمع کل:</span>
                        <span class="font-semibold"><?php echo formatCurrency($invoice['total_price']); ?></span>
                    </div>
                    <?php if ($invoice['discount'] > 0): ?>
                    <div class="flex justify-between text-red-600">
                        <span>تخفیف:</span>
                        <span><?php echo formatCurrency($invoice['discount']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>مبلغ قابل پرداخت:</span>
                        <span class="text-green-600"><?php echo formatCurrency($invoice['final_price']); ?></span>
                    </div>
                    <?php else: ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600">جمع کل:</span>
                        <span class="font-semibold"><?php echo formatCurrency($invoice['total_amount']); ?></span>
                    </div>
                    <?php if ($invoice['discount'] > 0): ?>
                    <div class="flex justify-between text-red-600">
                        <span>تخفیف:</span>
                        <span><?php echo formatCurrency($invoice['discount']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>مبلغ قابل پرداخت:</span>
                        <span class="text-green-600"><?php echo formatCurrency($invoice['final_amount']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t text-center text-sm text-gray-600">
            <p>با تشکر از اعتماد شما</p>
            <p class="mt-2">این فاکتور توسط سیستم مدیریت مرکز دندانپزشکی صادر شده است</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="max-w-4xl mx-auto mt-6 text-center no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition">
            چاپ فاکتور
        </button>
        <a href="../dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg transition inline-block mr-2">
            بازگشت
        </a>
    </div>
</body>
</html>
