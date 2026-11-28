<?php
// fetch_devices.php - Robust JSON asset fetcher
error_reporting(0);
header('Content-Type: application/json');
require_once 'auth.php';

// Check possible paths for db.php across different folder depths
$dbPath = null;
$possiblePaths = ['db.php', '../db.php', '../../db.php', '../../../db.php'];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $dbPath = $path;
        break;
    }
}

if (!$dbPath) {
    echo json_encode(['success' => false, 'message' => 'Database configuration file (db.php) not found.', 'data' => []]);
    exit;
}

require_once $dbPath;

try {
    $stmt = $pdo->query("SELECT id, asset_category, device_id, department_name, assigned_person, assigned_date FROM assigned_assets ORDER BY id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($rows as $r) {
        $data[] = [
            'id'           => $r['id'],
            'deviceId'     => $r['device_id'],
            'category'     => $r['asset_category'],
            'department'   => $r['department_name'],
            'person'       => $r['assigned_person'],
            'assignedDate' => $r['assigned_date']
        ];
    }

    echo json_encode([
        'success' => true,
        'data'    => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data'    => []
    ]);
}
?>