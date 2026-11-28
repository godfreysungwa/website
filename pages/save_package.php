<?php
header('Content-Type: application/json');
require_once 'auth.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

register_shutdown_function(function () {
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$transactionDate   = $_POST['transactionDate'] ?? null;
$movementType      = 'Stock Received';
$batchNo           = trim($_POST['batchNo'] ?? '');

$woodGlue          = !empty($_POST['woodGlue']) ? (float)$_POST['woodGlue'] : 0.00;
$polytheneBags     = !empty($_POST['polytheneBags']) ? (float)$_POST['polytheneBags'] : 0;
$plainBox          = !empty($_POST['plainBox']) ? (float)$_POST['plainBox'] : 0;
$plasticContainers = !empty($_POST['plasticContainers']) ? (float)$_POST['plasticContainers'] : 0;

$targetProduct     = trim($_POST['targetProduct'] ?? '');

// Retail Bags (1kg & 2kg)
$paperBagSize      = trim($_POST['paperBagSize'] ?? '');
$paperBagQty       = !empty($_POST['paperBagQty']) ? (float)$_POST['paperBagQty'] : 0;

// Bulk / Wholesale Bags (5kg to 50kg)
$bulkBagSize       = trim($_POST['bulkBagSize'] ?? '');
$bulkBagQty        = !empty($_POST['bulkBagQty']) ? (float)$_POST['bulkBagQty'] : 0;

$boxType           = trim($_POST['boxType'] ?? '');
$boxQty            = !empty($_POST['boxQty']) ? (float)$_POST['boxQty'] : 0;
$notes             = trim($_POST['notes'] ?? '');

// Reject if a client explicitly tried to submit a non-Stock Received movement type
if (isset($_POST['movementType']) && trim($_POST['movementType']) !== '' && trim($_POST['movementType']) !== 'Stock Received') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only "Stock Received" movement type is allowed here.'
    ]);
    exit;
}

// Mandatory core check
if (empty($transactionDate) || empty($batchNo)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please fill in all required fields (Date and Batch No).'
    ]);
    exit;
}

if (empty($targetProduct)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please select a Target Finished Product for Stock Received.'
    ]);
    exit;
}

// Ensure boxType has a sensible default when a specific product and boxQty are provided
if ($boxType === '' && $boxQty > 0) {
    if ($targetProduct === 'Sooji Chenga' || $targetProduct === 'Sooji Chenga Flour') {
        $boxType = 'Plain Box / Container';
    } else {
        $boxType = $targetProduct . ' Box';
    }
}

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO package_inventory 
            (transaction_date, movement_type, batch_no, wood_glue, polythene_bags, plain_box, plastic_containers, target_product, paper_bag_size, paper_bag_qty, bulk_bag_size, bulk_bag_qty, box_type, box_qty, notes) 
            VALUES (:transactionDate, :movementType, :batchNo, :woodGlue, :polytheneBags, :plainBox, :plasticContainers, :targetProduct, :paperBagSize, :paperBagQty, :bulkBagSize, :bulkBagQty, :boxType, :boxQty, :notes)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':transactionDate'   => $transactionDate,
        ':movementType'      => $movementType,
        ':batchNo'           => $batchNo,
        ':woodGlue'          => $woodGlue,
        ':polytheneBags'     => $polytheneBags,
        ':plainBox'          => $plainBox,
        ':plasticContainers' => $plasticContainers,
        ':targetProduct'     => $targetProduct,
        ':paperBagSize'      => $paperBagSize,
        ':paperBagQty'       => $paperBagQty,
        ':bulkBagSize'       => $bulkBagSize,
        ':bulkBagQty'        => $bulkBagQty,
        ':boxType'           => $boxType,
        ':boxQty'            => $boxQty,
        ':notes'             => $notes
    ]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Stock Received logged successfully!'
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to save record: ' . $e->getMessage()
    ]);
}