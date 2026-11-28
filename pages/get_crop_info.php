<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once '../../../db.php';

if (!isset($_GET['crop']) || empty($_GET['crop'])) {
    echo json_encode(['success' => false, 'message' => 'No crop selected']);
    exit;
}

$crop = trim($_GET['crop']);

try {
    // 1. Total Raw Received in Intake
    $stmtIntake = $pdo->prepare("
        SELECT COALESCE(SUM(gross_weight), 0) AS total_gross_weight, measurement_unit 
        FROM raw_material_intake 
        WHERE LOWER(TRIM(crop_type)) = LOWER(TRIM(:crop))
    ");
    $stmtIntake->execute([':crop' => $crop]);
    $intakeData = $stmtIntake->fetch(PDO::FETCH_ASSOC);

    $totalGrossWeight = $intakeData ? floatval($intakeData['total_gross_weight']) : 0.00;
    $unit = ($intakeData && !empty($intakeData['measurement_unit'])) ? $intakeData['measurement_unit'] : 'Kg';

    // 2. Total Raw Input taken into Cleaning processes
    $stmtCleaning = $pdo->prepare("
        SELECT COALESCE(SUM(raw_input), 0) AS total_raw_input 
        FROM cleaning_sorting 
        WHERE LOWER(TRIM(raw_source)) = LOWER(TRIM(:crop))
    ");
    $stmtCleaning->execute([':crop' => $crop]);
    $totalRawInput = floatval($stmtCleaning->fetchColumn());

    // 3. Remaining Uncleaned Raw Intake Stock
    $remainingIntakeStock = max(0, $totalGrossWeight - $totalRawInput);

    echo json_encode([
        'success'         => true,
        'crop'            => $crop,
        'total_intake'    => $totalGrossWeight,
        'total_cleaned_in'=> $totalRawInput,
        'remaining_stock' => $remainingIntakeStock,
        'unit'            => $unit
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database Query Error: ' . $e->getMessage()
    ]);
}
?>