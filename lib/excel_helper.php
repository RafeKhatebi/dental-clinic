<?php
/**
 * Excel Helper - Simple CSV Export
 */

class ExcelHelper {
    private $data = [];
    private $headers = [];
    
    public function setHeaders($headers) {
        $this->headers = $headers;
    }
    
    public function addRow($row) {
        $this->data[] = $row;
    }
    
    public function export($filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        
        if ($this->headers) {
            fputcsv($output, $this->headers);
        }
        
        foreach ($this->data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}
