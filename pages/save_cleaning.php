<?php
require_once 'auth.php';
require_once '../../../db.php'; // Database connection to nyirefami_db

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cleaningBatchNo = trim($_POST['cleaningBatchNo'] ?? '');
    $cleaningDate    = trim($_POST['cleaningDate'] ?? '');
    $rawSource       = trim($_POST['rawSource'] ?? '');
    $measurementUnit = trim($_POST['measurementUnit'] ?? 'Kg');
    $rawInput        = floatval($_POST['rawInput'] ?? 0);
    $cleanedOutput   = floatval($_POST['cleanedOutput'] ?? 0);
    $wastedAmount    = floatval($_POST['wastedAmount'] ?? 0);

    // Basic Validation
    if (empty($cleaningBatchNo) || empty($cleaningDate) || empty($rawSource) || $rawInput <= 0) {
        header("Location: cleaning.html?error=" . urlencode("Please fill in all required fields accurately."));
        exit;
    }

    try {
        // Step A: Fetch total gross_weight from raw_material_intake
        $stmtIntake = $pdo->prepare("
            SELECT COALESCE(SUM(gross_weight), 0) 
            FROM raw_material_intake 
            WHERE crop_type = :crop
        ");
        $stmtIntake->execute([':crop' => $rawSource]);
        $totalGrossWeight = floatval($stmtIntake->fetchColumn());

        // Step B: Fetch total raw_input from cleaning_sorting
        $stmtCleaning = $pdo->prepare("
            SELECT COALESCE(SUM(raw_input), 0) 
            FROM cleaning_sorting 
            WHERE raw_source = :crop
        ");
        $stmtCleaning->execute([':crop' => $rawSource]);
        $totalRawInput = floatval($stmtCleaning->fetchColumn());

        // Step C: Calculate remaining stock
        $remainingStock = $totalGrossWeight - $totalRawInput;

        // Step D: Block if user tries to process more than remaining stock
        if ($rawInput > $remainingStock) {
            $msg = "Insufficient stock! Remaining: " . number_format($remainingStock, 2) . " " . $measurementUnit . ", but attempted to clean: " . number_format($rawInput, 2) . " " . $measurementUnit;
            header("Location: cleaning.html?error=" . urlencode($msg));
            exit;
        }

        // Step E: Save to cleaning_sorting table
        $stmtInsert = $pdo->prepare("
            INSERT INTO cleaning_sorting (
                cleaning_batch_no, 
                cleaning_date, 
                raw_source, 
                measurement_unit, 
                raw_input, 
                cleaned_output, 
                wasted_amount
            ) VALUES (
                :batch_no, 
                :cleaning_date, 
                :raw_source, 
                :measurement_unit, 
                :raw_input, 
                :cleaned_output, 
                :wasted_amount
            )
        ");

        $stmtInsert->execute([
            ':batch_no'         => $cleaningBatchNo,
            ':cleaning_date'    => $cleaningDate,
            ':raw_source'       => $rawSource,
            ':measurement_unit' => $measurementUnit,
            ':raw_input'        => $rawInput,
            ':cleaned_output'   => $cleanedOutput,
            ':wasted_amount'    => $wastedAmount
        ]);

        header("Location: cleaning.html?status=success");
        exit;

    } catch (PDOException $e) {
        header("Location: cleaning.html?error=" . urlencode("Database Error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: cleaning.html");
    exit;
}
?>