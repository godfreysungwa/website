<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once '../../../db.php'; // Adjust path to db.php if inside another folder

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assetCategory  = trim($_POST['assetCategory'] ?? '');
    $deviceId       = trim($_POST['deviceId'] ?? '');
    $departmentName = trim($_POST['departmentName'] ?? '');
    $assignedPerson = trim($_POST['assignedPerson'] ?? '');
    $assignedDate   = date('Y-m-d');

    // Validation
    if (empty($assetCategory) || empty($deviceId) || empty($departmentName) || empty($assignedPerson)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    try {
        // Check if device_id already exists
        $checkStmt = $pdo->prepare("SELECT id FROM assigned_assets WHERE device_id = ?");
        $checkStmt->execute([$deviceId]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Device ID already registered.']);
            exit();
        }

        // Insert new record
        $stmt = $pdo->prepare("
            INSERT INTO assigned_assets (device_id, asset_category, department_name, assigned_person, assigned_date)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$deviceId, $assetCategory, $departmentName, $assignedPerson, $assignedDate]);

        echo json_encode([
            'success' => true,
            'message' => 'Asset registered successfully!',
            'data' => [
                'id' => $pdo->lastInsertId(),
                'deviceId' => $deviceId,
                'category' => $assetCategory,
                'department' => $departmentName,
                'person' => $assignedPerson,
                'assignedDate' => $assignedDate
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>