<?php
// export_excel.php - Clean Single-Sheet Exporter with Date Filters for Nyirefami
error_reporting(0);
ini_set('display_errors', 0);
require_once 'auth.php';
require_once '../../../db.php';

$report_type = $_GET['report_type'] ?? '';
$from_date   = $_GET['from_date'] ?? '';
$to_date     = $_GET['to_date'] ?? '';

// Helper function to build date filter clause
function getDateFilterSQL($dateCol) {
    global $from_date, $to_date;
    if (!empty($from_date) && !empty($to_date)) {
        return " WHERE " . $dateCol . " BETWEEN " . $GLOBALS['pdo']->quote($from_date) . " AND " . $GLOBALS['pdo']->quote($to_date);
    }
    return "";
}

if ($report_type === 'device_assets') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Nyirefami_Device_Report_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');

    try {
        $where = getDateFilterSQL('assigned_date');
        $stmt = $pdo->query("SELECT * FROM assigned_assets" . $where . " ORDER BY assigned_date ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $rows = []; }

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;}th{background:#b45309;color:#fff;font-weight:bold;padding:8px;border:1px solid #cbd5e1;}td{padding:6px;border:1px solid #cbd5e1;}</style></head>';
    echo '<body>';
    echo '<h2>Nyirefami Ltd - Device & Asset Inventory</h2>';
    echo '<table>';
    echo '<tr><th>Asset Category</th><th>Device ID / Tag</th><th>Department Name</th><th>Assigned Person</th><th>Date Assigned</th></tr>';
    if (count($rows) > 0) {
        foreach ($rows as $r) {
            echo '<tr><td>' . htmlspecialchars($r['asset_category'] ?? $r['category'] ?? '') . '</td><td><code>' . htmlspecialchars($r['device_id'] ?? $r['deviceId'] ?? '') . '</code></td><td>' . htmlspecialchars($r['department_name'] ?? $r['department'] ?? '') . '</td><td>' . htmlspecialchars($r['assigned_person'] ?? $r['person'] ?? '') . '</td><td>' . htmlspecialchars($r['assigned_date'] ?? '') . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="5" style="text-align:center;color:#6b7280;">No records found for the selected date range.</td></tr>';
    }
    echo '</table></body></html>';
    exit;

} elseif ($report_type === 'raw_material_intake') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Nyirefami_Raw_Material_Intake_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');

    try {
        $where = getDateFilterSQL('transaction_date');
        $rows = $pdo->query("SELECT * FROM raw_material_intake" . $where . " ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $rows = []; }

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;}th{background:#047857;color:#fff;font-weight:bold;padding:8px;border:1px solid #cbd5e1;}td{padding:6px;border:1px solid #cbd5e1;}</style></head>';
    echo '<body>';
    echo '<h2>Nyirefami Ltd - Raw Material Intake Report</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Transaction Date</th><th>Batch No</th><th>Crop Type</th><th>Supplier Name</th><th>Gross Weight</th><th>Measurement Unit</th><th>Notes</th><th>Created At</th></tr>';
    if (count($rows) > 0) {
        foreach ($rows as $r) {
            echo '<tr><td>' . htmlspecialchars($r['id'] ?? '') . '</td><td>' . htmlspecialchars($r['transaction_date'] ?? '') . '</td><td>' . htmlspecialchars($r['batch_no'] ?? '') . '</td><td>' . htmlspecialchars($r['crop_type'] ?? '') . '</td><td>' . htmlspecialchars($r['supplier_name'] ?? '') . '</td><td>' . htmlspecialchars($r['gross_weight'] ?? '') . '</td><td>' . htmlspecialchars($r['measurement_unit'] ?? '') . '</td><td>' . htmlspecialchars($r['notes'] ?? '') . '</td><td>' . htmlspecialchars($r['created_at'] ?? '') . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="9" style="text-align:center;color:#6b7280;">No records found.</td></tr>';
    }
    echo '</table></body></html>';
    exit;

} elseif ($report_type === 'cleaning_sorting') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Nyirefami_Cleaning_Sorting_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');

    try {
        $where = getDateFilterSQL('cleaning_date');
        $rows = $pdo->query("SELECT * FROM cleaning_sorting" . $where . " ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $rows = []; }

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;}th{background:#047857;color:#fff;font-weight:bold;padding:8px;border:1px solid #cbd5e1;}td{padding:6px;border:1px solid #cbd5e1;}</style></head>';
    echo '<body>';
    echo '<h2>Nyirefami Ltd - Cleaning & Sorting Report</h2>';
    echo '<table>';
    echo '<tr><th>Cleaning Batch No</th><th>Cleaning Date</th><th>Raw Source</th><th>Measurement Unit</th><th>Raw Input</th><th>Cleaned Output</th><th>Wasted Amount</th><th>Created At</th></tr>';
    if (count($rows) > 0) {
        foreach ($rows as $r) {
            echo '<tr><td>' . htmlspecialchars($r['cleaning_batch_no'] ?? '') . '</td><td>' . htmlspecialchars($r['cleaning_date'] ?? '') . '</td><td>' . htmlspecialchars($r['raw_source'] ?? '') . '</td><td>' . htmlspecialchars($r['measurement_unit'] ?? '') . '</td><td>' . htmlspecialchars($r['raw_input'] ?? '') . '</td><td>' . htmlspecialchars($r['cleaned_output'] ?? '') . '</td><td>' . htmlspecialchars($r['wasted_amount'] ?? '') . '</td><td>' . htmlspecialchars($r['created_at'] ?? '') . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="8" style="text-align:center;color:#6b7280;">No records found.</td></tr>';
    }
    echo '</table></body></html>';
    exit;

} elseif ($report_type === 'production_milling') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Nyirefami_Production_Milling_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');

    try {
        $where = getDateFilterSQL('production_date');
        $rows = $pdo->query("SELECT * FROM production_milling" . $where . " ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $rows = []; }

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;}th{background:#047857;color:#fff;font-weight:bold;padding:8px;border:1px solid #cbd5e1;}td{padding:6px;border:1px solid #cbd5e1;}</style></head>';
    echo '<body>';
    echo '<h2>Nyirefami Ltd - Production & Milling Report</h2>';
    echo '<table>';
    echo '<tr><th>Production Run ID</th><th>Production Date</th><th>Cleaned Crop Source</th><th>Measurement Unit</th><th>Cleaned Stock Used</th><th>Finished Product Name</th><th>Production Bag Size</th><th>Yield Output</th><th>Cartons / Bags Produced</th><th>Created At</th></tr>';
    
    if (count($rows) > 0) {
        foreach ($rows as $r) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['production_run_id'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['production_date'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['cleaned_crop_source'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['measurement_unit'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['cleaned_stock_used'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['finished_product_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['production_bag_size'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['yield_output'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['cartons_produced'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['created_at'] ?? '') . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="10" style="text-align:center;color:#6b7280;">No records found.</td></tr>';
    }
    echo '</table></body></html>';
    exit;

} elseif ($report_type === 'package_inventory') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Nyirefami_Package_Inventory_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');

    try {
        $where = getDateFilterSQL('transaction_date');
        $rows = $pdo->query("SELECT * FROM package_inventory" . $where . " ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $rows = []; }

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;}th{background:#047857;color:#fff;font-weight:bold;padding:8px;border:1px solid #cbd5e1;}td{padding:6px;border:1px solid #cbd5e1;}</style></head>';
    echo '<body>';
    echo '<h2>Nyirefami Ltd - Package & Consumables Inventory</h2>';
    echo '<table>';
    echo '<tr><th>Transaction Date</th><th>Batch No</th><th>Movement Type</th><th>Target Product</th><th>Retail Bag Size</th><th>Paper Bag Qty</th><th>Bulk Bag Size</th><th>Bulk Bag Qty</th><th>Box Type</th><th>Specific Box Qty</th><th>Plain Box</th><th>Plastic Containers</th><th>Polythene Bags</th><th>Wood Glue (L)</th><th>Notes</th></tr>';
    
    if (count($rows) > 0) {
        foreach ($rows as $r) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['transaction_date'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['batch_no'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['movement_type'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['target_product'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['paper_bag_size'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['paper_bag_qty'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['bulk_bag_size'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['bulk_bag_qty'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['box_type'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['box_qty'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['plain_box'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['plastic_containers'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['polythene_bags'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['wood_glue'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['notes'] ?? '') . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="15" style="text-align:center;color:#6b7280;">No records found.</td></tr>';
    }
    echo '</table></body></html>';
    exit;

} elseif ($report_type === 'sales_distribution') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Nyirefami_Sales_Distribution_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');

    try {
        $where = getDateFilterSQL('sale_date');
        $stmt = $pdo->query("SELECT * FROM sales" . $where . " ORDER BY id ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        try {
            $where = getDateFilterSQL('sale_date');
            $stmt = $pdo->query("SELECT * FROM sales_distribution" . $where . " ORDER BY id ASC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) { $rows = []; }
    }

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;}th{background:#047857;color:#fff;font-weight:bold;padding:8px;border:1px solid #cbd5e1;}td{padding:6px;border:1px solid #cbd5e1;}</style></head>';
    echo '<body>';
    echo '<h2>Nyirefami Ltd - Sales & Distribution Report</h2>';
    echo '<table>';
    echo '<tr><th>Sale Date</th><th>Customer Name</th><th>Product Sold</th><th>Movement Type</th><th>Available Cartons Before</th><th>Cartons Sold</th></tr>';
    if (count($rows) > 0) {
        foreach ($rows as $r) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['sale_date'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['customer_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['product_sold'] ?? $r['product_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['movement_type'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['available_cartons_before'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['cartons_sold'] ?? '') . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6" style="text-align:center;color:#6b7280;">No records found.</td></tr>';
    }
    echo '</table></body></html>';
    exit;
} else {
    header('Location: excel.html');
    exit;
}
?>