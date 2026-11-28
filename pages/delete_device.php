<?php
// delete_device.php
header('Content-Type: application/json');
require_once 'auth.php';
require_once '../../../db.php';

try {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        throw new Exception('Invalid asset ID provided.');
    }

    $stmt = $pdo->prepare("DELETE FROM assigned_assets WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Asset deleted successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>