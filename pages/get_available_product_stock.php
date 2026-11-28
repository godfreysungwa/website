<?php
header('Content-Type: application/json');
require_once 'auth.php';
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $dbPath = null;
    $possiblePaths = ['db.php', '../db.php', '../../db.php', '../../../db.php'];

    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $dbPath = $path;
            break;
        }
    }

    if (!$dbPath) {
        throw new Exception('db.php file was not found in any expected parent directories.');
    }
    require_once $dbPath;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

$product = trim($_GET['product'] ?? '');
$size    = trim($_GET['size'] ?? '');

if (empty($product)) {
    echo json_encode(['success' => false, 'available_stock' => 0]);
    exit;
}

try {
    // Extract numeric weight (e.g., "45" from "45kg")
    preg_match('/(\d+)/', $size, $matches);
    $bagWeight = isset($matches[1]) ? (int)$matches[1] : 2;
    $numericSize = $matches[1] ?? '';
    $isBulk = ($bagWeight >= 5);

    if (!empty($numericSize)) {
        // Select from bulk_bags_produced if bulk, or cartons_produced if retail
        $prodCol = $isBulk ? 'bulk_bags_produced' : 'cartons_produced';

        // Robust matching: strips 'kg' and spaces to compare pure numbers (e.g., "45")
        $stmtProd = $pdo->prepare("
            SELECT COALESCE(SUM({$prodCol}), 0) 
            FROM production_milling 
            WHERE LOWER(TRIM(finished_product_name)) = LOWER(TRIM(:product))
            AND REGEXP_REPLACE(LOWER(production_bag_size), '[^0-9]', '') = :numeric_size
        ");
        $stmtProd->execute([
            ':product'      => $product,
            ':numeric_size' => $numericSize
        ]);
        $totalProduced = (float) $stmtProd->fetchColumn();

        $stmtSales = $pdo->prepare("
            SELECT COALESCE(SUM(cartons_sold), 0) 
            FROM sales 
            WHERE LOWER(TRIM(product_sold)) = LOWER(TRIM(:product))
            AND REGEXP_REPLACE(LOWER(dispatch_bag_size), '[^0-9]', '') = :numeric_size
        ");
        $stmtSales->execute([
            ':product'      => $product,
            ':numeric_size' => $numericSize
        ]);
        $totalSold = (float) $stmtSales->fetchColumn();

    } else {
        // Fallback if no size specified
        $stmtProd = $pdo->prepare("
            SELECT COALESCE(SUM(cartons_produced), 0) + COALESCE(SUM(bulk_bags_produced), 0) 
            FROM production_milling 
            WHERE LOWER(TRIM(finished_product_name)) = LOWER(TRIM(:product))
        ");
        $stmtProd->execute([':product' => $product]);
        $totalProduced = (float) $stmtProd->fetchColumn();

        $stmtSales = $pdo->prepare("
            SELECT COALESCE(SUM(cartons_sold), 0) 
            FROM sales 
            WHERE LOWER(TRIM(product_sold)) = LOWER(TRIM(:product))
        ");
        $stmtSales->execute([':product' => $product]);
        $totalSold = (float) $stmtSales->fetchColumn();
    }

    $availableStock = max(0, $totalProduced - $totalSold);

    echo json_encode([
        'success'          => true,
        'product'          => $product,
        'size'             => $size,
        'total_produced'   => $totalProduced,
        'total_dispatched' => $totalSold,
        'available_stock'  => $availableStock
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Query Error: ' . $e->getMessage()]);
}
?>