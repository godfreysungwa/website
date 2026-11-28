<?php
header('Content-Type: application/json');
require_once 'auth.php';
ini_set('display_errors', 0);
error_reporting(E_ALL);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'PHP Fatal Error: ' . $error['message'] . ' in ' . basename($error['file']) . ' line ' . $error['line']
        ]);
    }
});

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
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saleDate        = trim($_POST['saleDate'] ?? '');
    $customerName    = trim($_POST['customerName'] ?? '');
    $productSold     = trim($_POST['productSold'] ?? '');
    $dispatchBagSize = trim($_POST['dispatchBagSize'] ?? '');
    $movementType    = trim($_POST['movementType'] ?? $_POST['movement_type'] ?? '');
    $quantitySold    = (float)($_POST['quantitySold'] ?? 0);

    // Validate mandatory fields including Dispatch Bag Size & Movement Type
    if (empty($saleDate) || empty($customerName) || empty($productSold) || empty($dispatchBagSize) || empty($movementType) || $quantitySold <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all mandatory fields accurately, including packaging size and movement type.']);
        exit;
    }

    $allowedMovements = ['Sale', 'Replaced', 'Open Box', 'Complimentary'];
    if (!in_array($movementType, $allowedMovements)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Movement Type selected.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Verify available stock for this exact product AND exact bag size pool
        // Check total produced for this product & size
        $stmtProd = $pdo->prepare("
            SELECT COALESCE(SUM(cartons_produced), 0) 
            FROM production_milling 
            WHERE LOWER(TRIM(finished_product_name)) = LOWER(TRIM(:product))
            AND LOWER(TRIM(production_bag_size)) = LOWER(TRIM(:bag_size))
        ");
        $stmtProd->execute([
            ':product'  => $productSold,
            ':bag_size' => $dispatchBagSize
        ]);
        $totalProduced = (float)$stmtProd->fetchColumn();

        // Check total previously sold/dispatched for this product & size
        // (Note: ensure your sales table has a dispatch_bag_size column, or adjust accordingly)
        $stmtSales = $pdo->prepare("
            SELECT COALESCE(SUM(cartons_sold), 0) 
            FROM sales 
            WHERE LOWER(TRIM(product_sold)) = LOWER(TRIM(:product))
            AND LOWER(TRIM(dispatch_bag_size)) = LOWER(TRIM(:bag_size))
        ");
        $stmtSales->execute([
            ':product'  => $productSold,
            ':bag_size' => $dispatchBagSize
        ]);
        $totalSold = (float)$stmtSales->fetchColumn();

        $availableStock = max(0, $totalProduced - $totalSold);

        if ($quantitySold > $availableStock) {
            $pdo->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => "Insufficient stock available for {$dispatchBagSize}! Current store stock: {$availableStock}."
            ]);
            exit;
        }

        // 2. Insert store dispatch record with dispatch_bag_size
        $sql = "INSERT INTO sales (
                    sale_date, customer_name, product_sold, 
                    dispatch_bag_size, movement_type, available_cartons_before, cartons_sold
                ) VALUES (
                    :sale_date, :customer_name, :product_sold, 
                    :dispatch_bag_size, :movement_type, :available_before, :cartons_sold
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':sale_date'         => $saleDate,
            ':customer_name'     => $customerName,
            ':product_sold'      => $productSold,
            ':dispatch_bag_size' => $dispatchBagSize,
            ':movement_type'     => $movementType,
            ':available_before'  => $availableStock,
            ':cartons_sold'      => $quantitySold
        ]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Store dispatch record saved and inventory balance deducted successfully!'
        ]);

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Database SQL Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
?>