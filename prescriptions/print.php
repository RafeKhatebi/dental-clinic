<?php
require_once '../config/config.php';

$prescriptionId = intval($_GET['id'] ?? 0);

if (empty($prescriptionId)) {
    redirect('/patients/index.php');
}

$prescription = fetchOne("
    SELECT p.*, 
           pat.first_name, pat.last_name, pat.age, pat.phone,
           u.full_name as dentist_name
    FROM prescriptions p
    JOIN patients pat ON p.patient_id = pat.id
    JOIN users u ON p.dentist_id = u.id
    WHERE p.id = ?
", [$prescriptionId]);

if (!$prescription) {
    redirect('/patients/index.php');
}

// Get prescription items
$items = fetchAll("SELECT * FROM prescription_items WHERE prescription_id = ?", [$prescriptionId]);

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
    <title>نسخه - <?php echo $prescription['prescription_code']; ?></title>
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
        <div class="bg-blue-600 text-white p-6">
            <h1 class="text-2xl font-bold text-center"><?php echo $clinicName; ?></h1>
            <?php if ($clinicAddress): ?>
            <p class="text-center text-sm mt-2"><?php echo $clinicAddress; ?></p>
            <?php endif; ?>
            <?php if ($clinicPhone): ?>
            <p class="text-center text-sm">تلفن: <?php echo $clinicPhone; ?></p>
            <?php endif; ?>
        </div>

        <!-- Prescription Info -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">کد نسخه</p>
                    <p class="font-semibold"><?php echo $prescription['prescription_code']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">تاریخ</p>
                    <p class="font-semibold"><?php echo formatDate($prescription['prescription_date']); ?></p>
                </div>
            </div>
        </div>

        <!-- Patient Info -->
        <div class="p-6 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold mb-3">اطلاعات بیمار</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">نام و نام خانوادگی</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($prescription['first_name'] . ' ' . $prescription['last_name']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">سن</p>
                    <p class="font-semibold"><?php echo $prescription['age'] ?: '-'; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">تلفن</p>
                    <p class="font-semibold"><?php echo $prescription['phone']; ?></p>
                </div>
            </div>
        </div>

        <!-- Diagnosis -->
        <?php if (!empty($prescription['diagnosis'])): ?>
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold mb-2">تشخیص</h2>
            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($prescription['diagnosis'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- Prescription Items -->
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4">℞ نسخه دارویی</h2>
            <?php if (empty($items)): ?>
                <p class="text-gray-500 text-center py-4">داروی تجویز شده‌ای وجود ندارد</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($items as $index => $item): ?>
                    <div class="border-r-4 border-blue-500 pr-4">
                        <p class="font-semibold text-lg"><?php echo ($index + 1) . '. ' . htmlspecialchars($item['medicine_name']); ?></p>
                        <?php if ($item['dosage']): ?>
                        <p class="text-gray-700 mt-1"><strong>دوز:</strong> <?php echo htmlspecialchars($item['dosage']); ?></p>
                        <?php endif; ?>
                        <?php if ($item['frequency']): ?>
                        <p class="text-gray-700"><strong>تعداد دفعات:</strong> <?php echo htmlspecialchars($item['frequency']); ?></p>
                        <?php endif; ?>
                        <?php if ($item['duration']): ?>
                        <p class="text-gray-700"><strong>مدت مصرف:</strong> <?php echo htmlspecialchars($item['duration']); ?></p>
                        <?php endif; ?>
                        <?php if ($item['instructions']): ?>
                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($item['instructions']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Notes -->
        <?php if (!empty($prescription['notes'])): ?>
        <div class="p-6 bg-yellow-50 border-t">
            <h2 class="text-lg font-semibold mb-2">توصیه‌ها</h2>
            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($prescription['notes'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="p-6 border-t">
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-sm text-gray-600">پزشک معالج</p>
                    <p class="font-semibold text-lg"><?php echo htmlspecialchars($prescription['dentist_name']); ?></p>
                </div>
                <div class="text-left">
                    <p class="text-sm text-gray-600 mb-8">امضا و مهر</p>
                    <div class="border-t border-gray-400 w-48"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="max-w-4xl mx-auto mt-6 text-center no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition">
            چاپ نسخه
        </button>
        <a href="../patients/view.php?id=<?php echo $prescription['patient_id']; ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg transition inline-block mr-2">
            بازگشت
        </a>
    </div>
</body>
</html>
