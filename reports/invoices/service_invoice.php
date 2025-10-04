<?php
require_once '../../config/config.php';

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

$invoiceNumber = 'SRV-' . str_pad($serviceId, 6, '0', STR_PAD_LEFT);
$subtotal = $service['total_price'];
$discount = $service['discount'];
$total = $subtotal - $discount;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتور خدمات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <div class="no-print flex gap-4 mb-4">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                چاپ
            </button>
            <a href="../../patients/view.php?id=<?php echo $service['patient_id']; ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                بازگشت
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-6">فاکتور خدمات دندانپزشکی</h1>
            <div class="border-b pb-4 mb-4">
                <p><strong>شماره فاکتور:</strong> <?php echo $invoiceNumber; ?></p>
                <p><strong>تاریخ:</strong> <?php echo $service['service_date']; ?></p>
            </div>
            <div class="border-b pb-4 mb-4">
                <p><strong>بیمار:</strong> <?php echo $service['first_name'] . ' ' . $service['last_name']; ?></p>
                <p><strong>کد بیمار:</strong> <?php echo $service['patient_code']; ?></p>
                <p><strong>دندانپزشک:</strong> <?php echo $service['dentist_name']; ?></p>
            </div>
            <table class="w-full mb-4">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-right">ردیف</th>
                        <th class="p-2 text-right">خدمت</th>
                        <th class="p-2 text-right">تعداد</th>
                        <th class="p-2 text-right">قیمت واحد</th>
                        <th class="p-2 text-right">جمع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="p-2">1</td>
                        <td class="p-2"><?php echo $service['service_name']; ?></td>
                        <td class="p-2"><?php echo $service['quantity']; ?></td>
                        <td class="p-2"><?php echo number_format($service['unit_price']); ?></td>
                        <td class="p-2"><?php echo number_format($service['total_price']); ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="text-left space-y-2">
                <p>جمع: <?php echo number_format($subtotal); ?> افغانی</p>
                <p>تخفیف: <?php echo number_format($discount); ?> افغانی</p>
                <p class="text-xl font-bold">جمع کل: <?php echo number_format($total); ?> افغانی</p>
            </div>
            <div class="mt-8 text-center">
                <p>امضا و مهر</p>
            </div>
        </div>
    </div>
</body>
</html>
