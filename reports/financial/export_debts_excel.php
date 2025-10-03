<?php
require_once '../../config/config.php';
require_once '../../lib/excel_helper.php';

$debts = fetchAll("
    SELECT p.patient_code, p.first_name, p.last_name, p.phone,
           SUM(py.amount - py.paid_amount) as debt,
           MIN(py.due_date) as due_date
    FROM payments py
    JOIN patients p ON py.patient_id = p.id
    WHERE py.status IN ('pending', 'partial', 'overdue')
    GROUP BY py.patient_id
    HAVING debt > 0
    ORDER BY due_date ASC
");

$excel = new ExcelHelper();
$excel->setHeaders(['کد بیمار', 'نام', 'نام خانوادگی', 'تلفن', 'بدهی', 'سررسید']);

foreach ($debts as $d) {
    $excel->addRow([
        $d['patient_code'],
        $d['first_name'],
        $d['last_name'],
        $d['phone'],
        $d['debt'],
        $d['due_date']
    ]);
}

$excel->export('debts_report_' . date('Y-m-d'));
