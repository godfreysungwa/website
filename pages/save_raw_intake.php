<?php
// save_raw_intake.php
require_once 'auth.php';
require_once '../../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve and sanitize inputs
    $transactionDate  = $_POST['transactionDate'] ?? null;
    $batchNo          = trim($_POST['batchNo'] ?? '');
    $cropType         = $_POST['cropType'] ?? null;
    $supplierName     = trim($_POST['supplierName'] ?? '');
    $grossWeight      = !empty($_POST['grossWeight']) ? (float)$_POST['grossWeight'] : 0.00;
    $measurementUnit  = $_POST['measurementUnit'] ?? 'Kg';
    $notes            = trim($_POST['notes'] ?? '');

    // Allowed crop list for server-side validation
    $allowedCrops = [
        'Ulezi', 'Uwele', 'Mtama', 'QPM', 
        'Maize', 'Yellow Corn', 'Ngano', 'Sooji', 'Ndizi', 'Sooji Chenga', 'Other'
    ];

    // Mandatory field and crop validation
    if (empty($transactionDate) || empty($batchNo) || empty($cropType) || empty($supplierName) || empty($measurementUnit) || $grossWeight <= 0) {
        die("Error: Please fill in all required fields correctly.");
    }

    if (!in_array($cropType, $allowedCrops)) {
        die("Error: Invalid crop type selected.");
    }

    // Insert record into database
    $sql = "INSERT INTO raw_material_intake (
                transaction_date, 
                batch_no, 
                crop_type, 
                supplier_name, 
                gross_weight, 
                measurement_unit, 
                notes
            ) VALUES (
                :transactionDate, 
                :batchNo, 
                :cropType, 
                :supplierName, 
                :grossWeight, 
                :measurementUnit, 
                :notes
            )";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':transactionDate' => $transactionDate,
            ':batchNo'         => $batchNo,
            ':cropType'        => $cropType,
            ':supplierName'    => $supplierName,
            ':grossWeight'     => $grossWeight,
            ':measurementUnit' => $measurementUnit,
            ':notes'           => $notes
        ]);

        header("Location: raw_intake.html?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error saving record: " . $e->getMessage());
    }
}
?>