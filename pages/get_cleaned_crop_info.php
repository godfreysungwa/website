<?php
header('Content-Type: application/json');
require_once 'auth.php';
// Path resolution scanner for db.php
$dbPath = null;
$possiblePaths = ['db.php', '../db.php', '../../db.php', '../../../db.php'];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $dbPath = $path;
        break;
    }
}

if (!$dbPath) {
    echo json_encode(['success' => false, 'message' => 'db.php not found']);
    exit;
}
require_once $dbPath;

if (!isset($_GET['crop']) || empty($_GET['crop'])) {
    echo json_encode(['success' => false, 'message' => 'No crop selected']);
    exit;
}

$crop = trim($_GET['crop']);
$lowerCrop = strtolower($crop);

try {
    // Check if crop is direct intake (Sooji or Sooji Chenga)
    $isDirectIntake = ($lowerCrop === 'sooji' || $lowerCrop === 'sooji chenga');

    if ($isDirectIntake) {
        // 1a. For Direct Intake crops: Get total gross_weight from raw_material_intake table
        $stmtIntake = $pdo->prepare("
            SELECT 
                COALESCE(SUM(gross_weight), 0) AS total_intake,
                measurement_unit 
            FROM raw_material_intake 
            WHERE LOWER(TRIM(crop_type)) = LOWER(TRIM(:crop))
        ");
        $stmtIntake->execute([':crop' => $crop]);
        $intakeData = $stmtIntake->fetch(PDO::FETCH_ASSOC);

        $totalStock = $intakeData ? floatval($intakeData['total_intake']) : 0.00;
        $unit = ($intakeData && !empty($intakeData['measurement_unit'])) ? $intakeData['measurement_unit'] : 'Kg';

    } else {
        // 1b. For Standard crops: Get total cleaned_output from cleaning_sorting table
        $stmtCleaning = $pdo->prepare("
            SELECT 
                COALESCE(SUM(cleaned_output), 0) AS total_cleaned_output,
                measurement_unit 
            FROM cleaning_sorting 
            WHERE LOWER(TRIM(raw_source)) = LOWER(TRIM(:crop))
        ");
        $stmtCleaning->execute([':crop' => $crop]);
        $cleaningData = $stmtCleaning->fetch(PDO::FETCH_ASSOC);

        $totalStock = $cleaningData ? floatval($cleaningData['total_cleaned_output']) : 0.00;
        $unit = ($cleaningData && !empty($cleaningData['measurement_unit'])) ? $cleaningData['measurement_unit'] : 'Kg';
    }

    // 2. Get total stock already consumed in production_milling
    $stmtProduction = $pdo->prepare("
        SELECT 
            COALESCE(SUM(cleaned_stock_used), 0) AS total_milled 
        FROM production_milling 
        WHERE LOWER(TRIM(cleaned_crop_source)) = LOWER(TRIM(:crop))
    ");
    $stmtProduction->execute([':crop' => $crop]);
    $totalMilled = floatval($stmtProduction->fetchColumn());

    // 3. Formula: Available Stock = Total Intake/Cleaned - Total Milled
    $availableStock = max(0, $totalStock - $totalMilled);

    echo json_encode([
        'success'                 => true,
        'crop'                    => $crop,
        'is_direct_intake'        => $isDirectIntake,
        'total_intake_or_cleaned' => $totalStock,
        'total_milled'            => $totalMilled,
        'available_cleaned_stock' => $availableStock,
        'available_stock'         => $availableStock,
        'unit'                    => $unit
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database Query Error: ' . $e->getMessage()
    ]);
}
?>