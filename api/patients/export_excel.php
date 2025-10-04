<?php
require_once '../../config/config.php';

if (!isLoggedIn()) {
    die('Unauthorized');
}

$search = $_GET['search'] ?? '';
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE patient_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam, $searchParam];
}

$patients = fetchAll("SELECT * FROM patients $whereClause ORDER BY created_at DESC", $params);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="patients_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

fputcsv($output, ['کد بیمار', 'نام', 'نام خانوادگی', 'سن', 'جنسیت', 'تلفن', 'ایمیل', 'آدرس', 'تاریخ ثبت']);

foreach ($patients as $p) {
    fputcsv($output, [
        $p['patient_code'],
        $p['first_name'],
        $p['last_name'],
        $p['age'] ?? '-',
        $p['gender'] == 'male' ? 'مرد' : ($p['gender'] == 'female' ? 'زن' : '-'),
        $p['phone'],
        $p['email'] ?? '-',
        $p['address'] ?? '-',
        $p['created_at']
    ]);
}

fclose($output);
