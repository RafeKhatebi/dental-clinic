<?php
require_once '../../config/config.php';

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$expenses = fetchAll("
    SELECT * FROM documents 
    WHERE document_type = 'expense' 
    AND next_due_date BETWEEN ? AND ?
    ORDER BY next_due_date, expense_category
", [$startDate, $endDate]);

$byCategory = [];
$total = 0;
foreach ($expenses as $exp) {
    $cat = $exp['expense_category'];
    if (!isset($byCategory[$cat])) {
        $byCategory[$cat] = ['items' => [], 'total' => 0];
    }
    $byCategory[$cat]['items'][] = $exp;
    $byCategory[$cat]['total'] += $exp['amount'];
    $total += $exp['amount'];
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش مصارف</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        @media print { .no-print { display: none; } aside, header { display: none; } }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="no-print flex gap-4 mb-4">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg">چاپ</button>
            <a href="../index.php" class="bg-gray-600 text-white px-6 py-2 rounded-lg">بازگشت</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <?php echo getInvoiceHeader(); ?>
            
            <h2 class="text-2xl font-bold text-center mb-6">گزارش مصارف</h2>
            <p class="text-center mb-6"><strong>از:</strong> <?php echo $startDate; ?> <strong>تا:</strong> <?php echo $endDate; ?></p>
            
            <div class="bg-red-50 p-4 rounded-lg text-center mb-6">
                <p class="text-sm text-gray-600">کل مصارف</p>
                <p class="text-3xl font-bold text-red-600"><?php echo formatCurrency($total); ?></p>
            </div>
            
            <?php foreach ($byCategory as $category => $data): ?>
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-3 bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($category); ?> - <?php echo formatCurrency($data['total']); ?></h3>
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border p-2 text-right">عنوان</th>
                            <th class="border p-2 text-right">نوع</th>
                            <th class="border p-2 text-right">تکرار</th>
                            <th class="border p-2 text-right">سررسید</th>
                            <th class="border p-2 text-right">مبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['items'] as $exp): ?>
                        <tr>
                            <td class="border p-2"><?php echo htmlspecialchars($exp['title']); ?></td>
                            <td class="border p-2"><?php echo $exp['expense_type'] == 'fixed' ? 'ثابت' : ($exp['expense_type'] == 'variable' ? 'متغیر' : 'یکبار'); ?></td>
                            <td class="border p-2"><?php echo $exp['recurrence'] == 'monthly' ? 'ماهانه' : ($exp['recurrence'] == 'yearly' ? 'سالانه' : 'یکبار'); ?></td>
                            <td class="border p-2"><?php echo $exp['next_due_date']; ?></td>
                            <td class="border p-2 font-semibold text-red-600"><?php echo formatCurrency($exp['amount']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
            
            <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                <div class="flex justify-between text-xl font-bold">
                    <span>جمع کل مصارف:</span>
                    <span class="text-red-600"><?php echo formatCurrency($total); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
