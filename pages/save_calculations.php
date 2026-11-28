<?php
// save_calculations.php - Updated with per-product packaging (no Maize / Yellow pools)
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'auth.php';

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
    $run_id              = trim($_POST['productionRunId'] ?? '');
    $prod_date           = trim($_POST['productionDate'] ?? '');
    $crop_source         = trim($_POST['cleanedCropSource'] ?? '');
    $unit                = trim($_POST['measurementUnit'] ?? 'Kg');
    $stock_used          = (float) ($_POST['cleanedStockUsed'] ?? 0);
    $product_name        = trim($_POST['finishedProductName'] ?? '');
    $yield_output        = (float) ($_POST['yieldOutput'] ?? 0);
    $output_qty          = (float) ($_POST['cartonsProduced'] ?? 0);
    $production_bag_size = trim($_POST['productionBagSize'] ?? '');

    if (empty($run_id) || empty($prod_date) || empty($crop_source) || empty($product_name) || $yield_output <= 0 || empty($production_bag_size)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields including finished product and packaging size.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Check if bulk run (5kg to 50kg)
        preg_match('/(\d+)/', $production_bag_size, $matches);
        $bag_weight_kg = isset($matches[1]) ? (float)$matches[1] : 2;
        $is_bulk_run = ($bag_weight_kg >= 5);

        $cartons_produced   = $is_bulk_run ? 0 : $output_qty;
        $bulk_bags_produced = $is_bulk_run ? $output_qty : 0;

        // 1. Save production milling entry with separate columns
        $prod_sql = "INSERT INTO production_milling (
                    production_run_id, 
                    production_date, 
                    cleaned_crop_source, 
                    measurement_unit, 
                    cleaned_stock_used, 
                    finished_product_name, 
                    production_bag_size,
                    yield_output,
                    cartons_produced,
                    bulk_bags_produced
                ) VALUES (
                    :run_id, :prod_date, :crop_source, :unit, :stock_used, :product_name, :bag_size, :yield_kg, :cartons, :bulk_bags
                )";
        
        $prod_stmt = $pdo->prepare($prod_sql);
        $prod_stmt->execute([
            ':run_id'       => $run_id,
            ':prod_date'    => $prod_date,
            ':crop_source'  => $crop_source,
            ':unit'         => $unit,
            ':stock_used'   => $stock_used,
            ':product_name' => $product_name,
            ':bag_size'     => $production_bag_size,
            ':yield_kg'     => $yield_output,
            ':cartons'      => $cartons_produced,
            ':bulk_bags'    => $bulk_bags_produced
        ]);

        // 2. Each product now has its own dedicated packaging (no Maize or Yellow Corn pools)
        $packaging_group = $product_name;
        if (stripos($product_name, 'Sooji Chenga') !== false) {
            $box_type = 'Plain Box / Container';
        } else {
            $box_type = $product_name . " Box";
        }

        if ($is_bulk_run) {
            $bag_size           = '';
            $bulk_bag_qty       = $output_qty;
            $wood_glue_liters   = 0;
            $paper_bag_qty      = 0;
            $box_qty            = 0;
            $plain_box_qty      = 0;
            $plastic_containers = 0;
        } else {
            $bulk_bag_qty    = 0;
            $is_sooji_chenga = (stripos($crop_source, 'Sooji Chenga') !== false || stripos($product_name, 'Sooji Chenga') !== false);
            $one_kg_group    = ['Ulezi', 'Finger Millet', 'Uwele', 'Pearl Millet', 'Mtama', 'Ndizi', 'Banana', 'Wheat Flour', 'Sooji'];
            
            $is_one_kg = false;
            foreach ($one_kg_group as $key) {
                if (stripos($crop_source, $key) !== false || stripos($product_name, $key) !== false) {
                    $is_one_kg = true;
                    break;
                }
            }

            $is_ulezi = (stripos($crop_source, 'Ulezi') !== false || stripos($product_name, 'Ulezi') !== false);
            $is_bag_metric = (strcasecmp($unit, 'Bag') === 0 || strcasecmp($unit, 'Bags') === 0);

            if ($is_ulezi && $is_bag_metric) {
                $cartons = ($output_qty > 0) ? $output_qty : ($yield_output * 10);
            } else {
                if ($is_one_kg || $is_sooji_chenga) {
                    $cartons = ($output_qty > 0) ? $output_qty : ceil($yield_output / 10);
                } else {
                    $cartons = ($output_qty > 0) ? $output_qty : ceil($yield_output / 20);
                }
            }

            if ($is_sooji_chenga) {
                $bag_size           = 'N/A';
                $wood_glue_liters   = $cartons * 0.005;
                $paper_bag_qty      = 0;
                $box_qty            = 0;
                $plain_box_qty      = $cartons * 1;
                $plastic_containers = $cartons * 12;
                $box_type           = 'Plain Box / Container';
            } else if ($is_one_kg) {
                $bag_size           = $product_name . ' Paper Bag (1kg)';
                $wood_glue_liters   = $cartons * (1 / 100);
                $paper_bag_qty      = $cartons * (1000 / 100);
                $box_qty            = $cartons * (100 / 100);
                $plain_box_qty      = 0;
                $plastic_containers = 0;
            } else {
                // 2kg and other products use their own product name & 2kg bag rule
                $bag_size           = $product_name . ' Paper Bag (2kg)';
                $wood_glue_liters   = $cartons * (1 / 100);
                $paper_bag_qty      = $cartons * (2000 / 100);
                $box_qty            = $cartons * (50 / 100);
                $plain_box_qty      = 0;
                $plastic_containers = 0;
            }
        }

        // 4. Aggregate TOTAL RECEIVED STOCK for this specific product
        $sum_received_sql = "SELECT 
                                COALESCE(SUM(paper_bag_qty), 0) AS total_paper_bags,
                                COALESCE(SUM(bulk_bag_qty), 0) AS total_bulk_bags,
                                COALESCE(SUM(box_qty), 0) AS total_boxes,
                                COALESCE(SUM(polythene_bags), 0) AS total_polythene,
                                COALESCE(SUM(plain_box), 0) AS total_plain_box,
                                COALESCE(SUM(plastic_containers), 0) AS total_plastic_containers
                               FROM package_inventory 
                               WHERE movement_type = 'Stock Received' 
                               AND LOWER(target_product) = LOWER(:product_name)
                               AND (:is_bulk = 0 OR LOWER(bulk_bag_size) = LOWER(:bulk_size))";

        $sum_recv_stmt = $pdo->prepare($sum_received_sql);
        $sum_recv_stmt->execute([
            ':product_name' => $product_name,
            ':is_bulk'      => $is_bulk_run ? 1 : 0,
            ':bulk_size'    => $production_bag_size
        ]);
        $total_received = $sum_recv_stmt->fetch(PDO::FETCH_ASSOC);

        $glue_received_stmt = $pdo->query("SELECT COALESCE(SUM(wood_glue), 0) AS total_glue FROM package_inventory WHERE movement_type = 'Stock Received'");
        $total_glue_received = (float) $glue_received_stmt->fetch(PDO::FETCH_ASSOC)['total_glue'];

        // 5. Aggregate TOTAL PREVIOUS CONSUMPTION for this specific product
        $sum_consumed_sql = "SELECT 
                                COALESCE(SUM(paper_bag_qty), 0) AS consumed_paper_bags,
                                COALESCE(SUM(bulk_bag_qty), 0) AS consumed_bulk_bags,
                                COALESCE(SUM(box_qty), 0) AS consumed_boxes,
                                COALESCE(SUM(plain_box), 0) AS consumed_plain_box,
                                COALESCE(SUM(plastic_containers), 0) AS consumed_plastic_containers
                               FROM package_inventory 
                               WHERE movement_type = 'Consumed' 
                               AND LOWER(target_product) = LOWER(:product_name)
                               AND (:is_bulk = 0 OR LOWER(bulk_bag_size) = LOWER(:bulk_size))";

        $sum_cons_stmt = $pdo->prepare($sum_consumed_sql);
        $sum_cons_stmt->execute([
            ':product_name' => $product_name,
            ':is_bulk'      => $is_bulk_run ? 1 : 0,
            ':bulk_size'    => $production_bag_size
        ]);
        $total_consumed_before = $sum_cons_stmt->fetch(PDO::FETCH_ASSOC);

        $glue_consumed_stmt = $pdo->query("SELECT COALESCE(SUM(wood_glue), 0) AS total_glue_consumed FROM package_inventory WHERE movement_type = 'Consumed'");
        $total_glue_consumed_before = (float) $glue_consumed_stmt->fetch(PDO::FETCH_ASSOC)['total_glue_consumed'];

        // 6. Live Remaining Balances
        $init_bags    = (float) $total_received['total_paper_bags'];
        $init_bulk    = (float) $total_received['total_bulk_bags'];
        $init_boxes   = (float) $total_received['total_boxes'];
        $init_poly    = (float) $total_received['total_polythene'];
        $init_plain   = (float) $total_received['total_plain_box'];
        $init_plastic = (float) $total_received['total_plastic_containers'];

        $consumed_bags_before    = (float) $total_consumed_before['consumed_paper_bags'];
        $consumed_bulk_before    = (float) $total_consumed_before['consumed_bulk_bags'];
        $consumed_boxes_before   = (float) $total_consumed_before['consumed_boxes'];
        $consumed_plain_before   = (float) $total_consumed_before['consumed_plain_box'];
        $consumed_plastic_before = (float) $total_consumed_before['consumed_plastic_containers'];

        $current_avail_bags    = max(0, $init_bags - $consumed_bags_before);
        $current_avail_bulk    = max(0, $init_bulk - $consumed_bulk_before);
        $current_avail_boxes   = max(0, $init_boxes - $consumed_boxes_before);
        $current_avail_plain   = max(0, $init_plain - $consumed_plain_before);
        $current_avail_plastic = max(0, $init_plastic - $consumed_plastic_before);
        $current_avail_glue    = max(0, $total_glue_received - $total_glue_consumed_before);

        if ($is_bulk_run) {
            if ($bulk_bag_qty > $current_avail_bulk) {
                $pdo->rollBack();
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Insufficient bulk bag stock for ' . $product_name . ' (' . $production_bag_size . ')! Required: ' . number_format($bulk_bag_qty) . '. Available: ' . number_format($current_avail_bulk) . '.'
                ]);
                exit;
            }
        } else {
            if ($paper_bag_qty > $current_avail_bags || $box_qty > $current_avail_boxes || $wood_glue_liters > $current_avail_glue) {
                $pdo->rollBack();
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Insufficient packaging stock for ' . $product_name . '! Required: ' . number_format($paper_bag_qty) . ' bags, ' . number_format($box_qty) . ' boxes, ' . number_format($wood_glue_liters, 2) . 'L glue.'
                ]);
                exit;
            }
        }

        $total_consumed_bags    = $consumed_bags_before + $paper_bag_qty;
        $total_consumed_bulk    = $consumed_bulk_before + $bulk_bag_qty;
        $total_consumed_boxes   = $consumed_boxes_before + $box_qty;
        $total_consumed_plain   = $consumed_plain_before + $plain_box_qty;
        $total_consumed_plastic = $consumed_plastic_before + $plastic_containers;

        $rem_bags    = max(0, $init_bags - $total_consumed_bags);
        $rem_bulk    = max(0, $init_bulk - $total_consumed_bulk);
        $rem_boxes   = max(0, $init_boxes - $total_consumed_boxes);
        $rem_plain   = max(0, $init_plain - $total_consumed_plain);
        $rem_plastic = max(0, $init_plastic - $total_consumed_plastic);
        $rem_glue    = max(0, $current_avail_glue - $wood_glue_liters);

        // 7. Record Consumed
        $insert_consumed = "INSERT INTO package_inventory (
                            transaction_date, batch_no, target_product, paper_bag_size, 
                            paper_bag_qty, bulk_bag_size, bulk_bag_qty, box_type, box_qty, wood_glue, 
                            polythene_bags, plain_box, plastic_containers, movement_type, notes
                        ) VALUES (
                            :trans_date, :batch_no, :product, :bag_size, 
                            :bags, :bulk_size, :bulk_bags, :box_type, :boxes, :glue, 
                            0, :plain, :plastic, 'Consumed', :notes
                        )";
        $consumed_stmt = $pdo->prepare($insert_consumed);
        $consumed_stmt->execute([
            ':trans_date' => $prod_date,
            ':batch_no'   => $run_id,
            ':product'    => $packaging_group,
            ':bag_size'   => $is_bulk_run ? '' : $bag_size,
            ':bags'       => $is_bulk_run ? 0 : $paper_bag_qty,
            ':bulk_size'  => $is_bulk_run ? $production_bag_size : '',
            ':bulk_bags'  => $is_bulk_run ? $bulk_bag_qty : 0,
            ':box_type'   => $box_type,
            ':boxes'      => $box_qty,
            ':glue'       => $wood_glue_liters,
            ':plain'      => $plain_box_qty,
            ':plastic'    => $plastic_containers,
            ':notes'      => "Deducted for Production Run (" . $product_name . "): " . $run_id
        ]);

        // 8. Record Remained Stock
        $insert_remained = "INSERT INTO package_inventory (
                            transaction_date, batch_no, target_product, paper_bag_size, 
                            paper_bag_qty, bulk_bag_size, bulk_bag_qty, box_type, box_qty, wood_glue, 
                            polythene_bags, plain_box, plastic_containers, movement_type, notes
                        ) VALUES (
                            :trans_date, :batch_no, :product, :bag_size, 
                            :bags, :bulk_size, :bulk_bags, :box_type, :boxes, :glue, 
                            :poly, :plain, :plastic, 'Stock Remained', :notes
                        )";
        $remained_stmt = $pdo->prepare($insert_remained);
        $remained_stmt->execute([
            ':trans_date' => $prod_date,
            ':batch_no'   => $run_id,
            ':product'    => $packaging_group,
            ':bag_size'   => $is_bulk_run ? '' : $bag_size,
            ':bags'       => $is_bulk_run ? 0 : $rem_bags,
            ':bulk_size'  => $is_bulk_run ? $production_bag_size : '',
            ':bulk_bags'  => $is_bulk_run ? $rem_bulk : 0,
            ':box_type'   => $box_type,
            ':boxes'      => $rem_boxes,
            ':glue'       => $rem_glue,
            ':poly'       => $init_poly,
            ':plain'      => $rem_plain,
            ':plastic'    => $rem_plastic,
            ':notes'      => "Net Stock Remained after Run (" . $product_name . "): " . $run_id
        ]);

        $pdo->commit();

        // 9. Dynamic Tailored Deduction Details
        $deductedItems = [];
        $deductedItems[] = number_format($stock_used, 2) . " " . $unit . " of Cleaned " . $crop_source;

        if ($is_bulk_run) {
            $deductedItems[] = number_format($bulk_bag_qty) . " Bulk Sacks (" . $production_bag_size . " - " . $product_name . ")";
        } else {
            if ($is_sooji_chenga) {
                $deductedItems[] = number_format($plastic_containers) . " Plastic Containers";
                $deductedItems[] = number_format($plain_box_qty) . " Plain Boxes";
            } else {
                $deductedItems[] = number_format($paper_bag_qty) . " Paper Bags (" . $production_bag_size . " - " . $product_name . ")";
                $deductedItems[] = number_format($box_qty) . " Boxes (" . $box_type . ")";
                $deductedItems[] = number_format($wood_glue_liters, 2) . " Liters Wood Glue";
            }
        }

        echo json_encode([
            'status'                 => 'success',
            'message'                => 'Production recorded successfully and packaging inventory updated!',
            'is_bulk'                => $is_bulk_run,
            'deducted_details'       => $deductedItems,
            'current_run_deductions' => [
                'bag_size'          => $production_bag_size,
                'output_calculated' => $output_qty
            ]
        ]);

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Database SQL Error: ' . $e->getMessage()]);
    }
}
?>