<?php
// get_dashboard_stats.php - Updated for per-product packaging & accurate stock balances
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

session_start();

// Enforce login check for dashboard access
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'unauthorized',
        'message' => 'Session expired. Please log in again.'
    ]);
    exit;
}
require_once 'auth.php';
require_once '../../../db.php';

try {
    // 0. Raw Material Remained in Stock
    $raw_stock_stmt = $pdo->query("
        SELECT 
            r.crop_type AS crop_name,
            COALESCE(r.measurement_unit, 'Kg') AS unit,
            (
                COALESCE(SUM(r.gross_weight), 0) 
                - CASE 
                    WHEN LOWER(r.crop_type) IN ('sooji', 'sooji chenga') THEN COALESCE(pm_direct.total_used, 0)
                    ELSE COALESCE(c.total_cleaned_sent, 0)
                  END
            ) AS net_raw_stock
        FROM raw_material_intake r
        LEFT JOIN (
            SELECT raw_source, SUM(raw_input) AS total_cleaned_sent 
            FROM cleaning_sorting 
            GROUP BY raw_source
        ) c ON r.crop_type = c.raw_source
        LEFT JOIN (
            SELECT cleaned_crop_source, SUM(cleaned_stock_used) AS total_used 
            FROM production_milling 
            GROUP BY cleaned_crop_source
        ) pm_direct ON r.crop_type = pm_direct.cleaned_crop_source
        GROUP BY r.crop_type, COALESCE(r.measurement_unit, 'Kg'), c.total_cleaned_sent, pm_direct.total_used
        HAVING net_raw_stock > 0
    ");
    $raw_stock_breakdown = $raw_stock_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 1. Cleaned Stock Ready
    $clean_stmt = $pdo->query("
        SELECT 
            cs.raw_source AS crop_name,
            COALESCE(cs.measurement_unit, 'Kg') AS unit,
            (COALESCE(SUM(cs.cleaned_output), 0) - COALESCE(pm.total_used, 0)) AS net_cleaned_stock
        FROM cleaning_sorting cs
        LEFT JOIN (
            SELECT cleaned_crop_source, SUM(cleaned_stock_used) AS total_used 
            FROM production_milling 
            GROUP BY cleaned_crop_source
        ) pm ON cs.raw_source = pm.cleaned_crop_source
        GROUP BY cs.raw_source, COALESCE(cs.measurement_unit, 'Kg'), pm.total_used
        HAVING net_cleaned_stock > 0
    ");
    $cleaned_stocks = $clean_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Net Stock Remaining per Product and Packaging Size (Cartons vs Bulk Bags)
    $carton_stmt = $pdo->query("
        SELECT 
            p.finished_product_name AS product_name,
            p.production_bag_size AS bag_size,
            (
                COALESCE(SUM(p.cartons_produced), 0) + COALESCE(SUM(p.bulk_bags_produced), 0) 
                - COALESCE(s.total_sold, 0)
            ) AS net_stock
        FROM production_milling p
        LEFT JOIN (
            SELECT product_sold, dispatch_bag_size, SUM(cartons_sold) AS total_sold 
            FROM sales 
            GROUP BY product_sold, dispatch_bag_size
        ) s ON LOWER(TRIM(p.finished_product_name)) = LOWER(TRIM(s.product_sold)) 
           AND LOWER(TRIM(p.production_bag_size)) = LOWER(TRIM(s.dispatch_bag_size))
        GROUP BY p.finished_product_name, p.production_bag_size, s.total_sold
        HAVING net_stock > 0
    ");
    $carton_breakdown = $carton_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Packaging & Consumables per Target Product (Received minus Consumed)
    $pkg_stmt = $pdo->query("
        SELECT 
            target_product,
            MAX(NULLIF(TRIM(paper_bag_size), '')) AS paper_bag_size,
            MAX(NULLIF(TRIM(bulk_bag_size), '')) AS bulk_bag_size,
            MAX(NULLIF(TRIM(box_type), '')) AS box_type,
            GREATEST(0, SUM(CASE WHEN movement_type = 'Stock Received' THEN paper_bag_qty ELSE 0 END)
                     - SUM(CASE WHEN movement_type = 'Consumed' THEN paper_bag_qty ELSE 0 END)) AS paper_bag_qty,
            GREATEST(0, SUM(CASE WHEN movement_type = 'Stock Received' THEN bulk_bag_qty ELSE 0 END)
                     - SUM(CASE WHEN movement_type = 'Consumed' THEN bulk_bag_qty ELSE 0 END)) AS bulk_bag_qty,
            GREATEST(0, SUM(CASE WHEN movement_type = 'Stock Received' THEN box_qty ELSE 0 END)
                     - SUM(CASE WHEN movement_type = 'Consumed' THEN box_qty ELSE 0 END)) AS box_qty,
            GREATEST(0, SUM(CASE WHEN movement_type = 'Stock Received' THEN plain_box ELSE 0 END)
                     - SUM(CASE WHEN movement_type = 'Consumed' THEN plain_box ELSE 0 END)) AS plain_box,
            GREATEST(0, SUM(CASE WHEN movement_type = 'Stock Received' THEN plastic_containers ELSE 0 END)
                     - SUM(CASE WHEN movement_type = 'Consumed' THEN plastic_containers ELSE 0 END)) AS plastic_containers
        FROM package_inventory
        WHERE movement_type IN ('Stock Received', 'Consumed')
          AND TRIM(COALESCE(target_product, '')) <> ''
        GROUP BY target_product
        HAVING (paper_bag_qty > 0 OR bulk_bag_qty > 0 OR box_qty > 0 OR plain_box > 0 OR plastic_containers > 0)
        ORDER BY target_product ASC
    ");
    $raw_package_breakdown = $pkg_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format Sooji Chenga specifically to exclude paper bags and retail boxes
    $package_breakdown = array_map(function ($row) {
        if (stripos($row['target_product'], 'Sooji Chenga') !== false) {
            $row['paper_bag_size'] = 'N/A';
            $row['paper_bag_qty']  = 0;
            $row['box_qty']        = 0;
        }
        return $row;
    }, $raw_package_breakdown);

    // 3b. Universal Store Pool Glue Balance (Received minus Consumed)
    $glue_pool_stmt = $pdo->query("
        SELECT GREATEST(0,
            COALESCE(SUM(CASE WHEN movement_type = 'Stock Received' THEN wood_glue ELSE 0 END), 0)
            - COALESCE(SUM(CASE WHEN movement_type = 'Consumed' THEN wood_glue ELSE 0 END), 0)
        ) AS remaining_glue
        FROM package_inventory
    ");
    $glue_row = $glue_pool_stmt->fetch(PDO::FETCH_ASSOC);
    $universal_remaining_glue = $glue_row ? (float)$glue_row['remaining_glue'] : 0.00;

    // 4. Recent Operational Activities (Size & Unit Aware)
    $recent_activities = [];
    try {
        $recent_stmt = $pdo->query("
            SELECT 'Intake' AS activity_type, batch_no AS ref_id, CONCAT(crop_type, ' (', gross_weight, ' ', COALESCE(measurement_unit, 'Kg'), ')') AS details, created_at FROM raw_material_intake
            UNION ALL
            SELECT 'Production' AS activity_type, production_run_id AS ref_id, CONCAT(finished_product_name, ' (', production_bag_size, ') - ', CASE WHEN cartons_produced > 0 THEN CONCAT(cartons_produced, ' Cartons') ELSE CONCAT(bulk_bags_produced, ' Bulk Bags') END) AS details, created_at FROM production_milling
            UNION ALL
            SELECT 'Sales' AS activity_type, customer_name AS ref_id, CONCAT(product_sold, ' (', dispatch_bag_size, ') - ', cartons_sold, ' Units Sold') AS details, created_at FROM sales
            ORDER BY created_at DESC LIMIT 5
        ");
        $recent_activities = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        $recent_activities = [];
    }

    // Capture Logged-in User Data for Dashboard Display
    $logged_in_user = [
        'full_name' => $_SESSION['full_name'] ?? 'System User',
        'role'      => $_SESSION['role']      ?? 'StoreKeeper'
    ];

    echo json_encode([
        'status' => 'success',
        'user' => $logged_in_user,
        'has_data' => (count($raw_stock_breakdown) > 0 || count($cleaned_stocks) > 0 || count($carton_breakdown) > 0 || count($package_breakdown) > 0 || $universal_remaining_glue > 0),
        'raw_stock_breakdown' => $raw_stock_breakdown,
        'cleaned_stocks' => $cleaned_stocks,
        'carton_breakdown' => $carton_breakdown,
        'package_breakdown' => $package_breakdown,
        'universal_remaining_glue' => $universal_remaining_glue,
        'recent_activities' => $recent_activities
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>