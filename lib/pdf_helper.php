<?php
/**
 * PDF Helper Class - Minimal Version
 */

class SimplePDF {
    private $clinicInfo;
    private $content = [];
    
    public function __construct() {
        $this->loadClinicInfo();
    }
    
    private function loadClinicInfo() {
        require_once dirname(__DIR__) . '/config/config.php';
        $this->clinicInfo = [
            'name' => getSetting('clinic_name', 'مرکز دندانپزشکی'),
            'address' => getSetting('clinic_address', ''),
            'phone' => getSetting('clinic_phone', ''),
        ];
    }
    
    public function addHeader($title) {
        $this->content[] = ['type' => 'header', 'text' => $title];
    }
    
    public function addText($text) {
        $this->content[] = ['type' => 'text', 'text' => $text];
    }
    
    public function addTable($headers, $data) {
        $this->content[] = ['type' => 'table', 'headers' => $headers, 'data' => $data];
    }
    
    public function generateHTML() {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
        $html .= '<style>
            body { font-family: Tahoma, Arial; direction: rtl; text-align: right; }
            .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
            th { background-color: #f2f2f2; }
        </style></head><body>';
        
        $html .= '<div class="header">';
        $html .= '<h2>' . $this->clinicInfo['name'] . '</h2>';
        $html .= '<p>' . $this->clinicInfo['phone'] . ' | ' . $this->clinicInfo['address'] . '</p>';
        $html .= '</div>';
        
        foreach ($this->content as $item) {
            if ($item['type'] == 'header') {
                $html .= '<h3>' . $item['text'] . '</h3>';
            } elseif ($item['type'] == 'text') {
                $html .= '<p>' . $item['text'] . '</p>';
            } elseif ($item['type'] == 'table') {
                $html .= '<table><thead><tr>';
                foreach ($item['headers'] as $h) {
                    $html .= '<th>' . $h . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                foreach ($item['data'] as $row) {
                    $html .= '<tr>';
                    foreach ($row as $cell) {
                        $html .= '<td>' . $cell . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
            }
        }
        
        $html .= '</body></html>';
        return $html;
    }
    
    public function output($filename = 'document.html') {
        header('Content-Type: text/html; charset=utf-8');
        echo $this->generateHTML();
    }
    
    public function save($filename) {
        $filepath = dirname(__DIR__) . '/exports/pdf/' . $filename;
        file_put_contents($filepath, $this->generateHTML());
        return $filepath;
    }
}

function generateInvoiceNumber($prefix = 'INV') {
    return $prefix . '-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
}

function numberToWords($number) {
    $ones = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
    $tens = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
    $hundreds = ['', 'یکصد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];
    
    if ($number == 0) return 'صفر';
    if ($number < 10) return $ones[$number];
    if ($number < 100) {
        $t = floor($number / 10);
        $o = $number % 10;
        return $tens[$t] . ($o ? ' و ' . $ones[$o] : '');
    }
    if ($number < 1000) {
        $h = floor($number / 100);
        $rest = $number % 100;
        return $hundreds[$h] . ($rest ? ' و ' . numberToWords($rest) : '');
    }
    
    return number_format($number);
}
