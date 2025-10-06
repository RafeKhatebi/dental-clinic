<?php
/**
 * تبدیل جدول به Card Layout برای موبایل
 * استفاده: در صفحات لیست برای نمایش responsive
 */

function renderTableCard($items, $config) {
    if (empty($items)) return '';
    
    $html = '<div class="cards-mobile space-y-4 p-4">';
    
    foreach ($items as $item) {
        $html .= '<div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">';
        
        // Checkbox
        if ($config['checkbox'] ?? false) {
            $html .= '<div class="flex items-center justify-between mb-3 pb-3 border-b">';
            $html .= '<input type="checkbox" class="row-checkbox w-5 h-5" value="' . $item['id'] . '" onchange="updateBulkButtons()">';
            $html .= '<div class="flex gap-2">';
            
            // Actions
            foreach ($config['actions'] as $action) {
                $url = str_replace('{id}', $item['id'], $action['url']);
                if ($action['type'] === 'link') {
                    $html .= '<a href="' . $url . '" class="text-' . $action['color'] . '-600 hover:text-' . $action['color'] . '-900 text-sm font-medium">' . $action['label'] . '</a>';
                } else {
                    $html .= '<button onclick="' . str_replace('{id}', $item['id'], $action['onclick']) . '" class="text-' . $action['color'] . '-600 hover:text-' . $action['color'] . '-900 text-sm font-medium">' . $action['label'] . '</button>';
                }
            }
            
            $html .= '</div></div>';
        }
        
        // Fields
        foreach ($config['fields'] as $field) {
            $value = $item[$field['key']] ?? '-';
            
            // Format value
            if (isset($field['format'])) {
                if ($field['format'] === 'currency') {
                    $value = formatCurrency($value);
                } elseif ($field['format'] === 'date') {
                    $value = formatDate($value);
                } elseif ($field['format'] === 'badge') {
                    $badgeClass = $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    $badgeText = $value ? ($field['badge_true'] ?? 'فعال') : ($field['badge_false'] ?? 'غیرفعال');
                    $value = '<span class="px-2 py-1 text-xs rounded-full ' . $badgeClass . '">' . $badgeText . '</span>';
                } elseif ($field['format'] === 'custom' && isset($field['callback'])) {
                    $value = call_user_func($field['callback'], $item);
                }
            }
            
            $html .= '<div class="flex justify-between items-center py-2 border-b last:border-b-0">';
            $html .= '<span class="text-sm text-gray-600 font-medium">' . $field['label'] . ':</span>';
            $html .= '<span class="text-sm text-gray-900 font-semibold">' . $value . '</span>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * نمایش Pagination برای موبایل
 */
function renderMobilePagination($pagination) {
    if ($pagination['totalPages'] <= 1) return '';
    
    $current = $pagination['currentPage'];
    $total = $pagination['totalPages'];
    $prev = $current > 1 ? $current - 1 : 1;
    $next = $current < $total ? $current + 1 : $total;
    
    $html = '<div class="pagination-mobile flex items-center justify-between p-4 bg-white border-t">';
    
    // دکمه قبلی
    $prevDisabled = $current === 1 ? 'opacity-50 pointer-events-none' : '';
    $html .= '<a href="?page=' . $prev . '" class="px-4 py-2 bg-blue-600 text-white rounded-lg ' . $prevDisabled . '">قبلی</a>';
    
    // شماره صفحه
    $html .= '<span class="text-sm text-gray-600">صفحه ' . $current . ' از ' . $total . '</span>';
    
    // دکمه بعدی
    $nextDisabled = $current === $total ? 'opacity-50 pointer-events-none' : '';
    $html .= '<a href="?page=' . $next . '" class="px-4 py-2 bg-blue-600 text-white rounded-lg ' . $nextDisabled . '">بعدی</a>';
    
    $html .= '</div>';
    
    return $html;
}
?>
