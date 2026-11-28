<?php
// backup_db.php - Generates and downloads a complete SQL backup of nyirefami_db
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once 'auth.php';
require_once '../../../db.php';

$db_host = 'localhost';$db_user = 'root';
$db_pass = '';$db_name = 'nyirefami_db';

$backup_filename =$db_name . '_backup_' . date('Y-m-d_H-i-s') . '.sql';

// Method 1: Attempt native mysqldump execution
$mysqldump_path = 'mysqldump';
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && file_exists('C:\\xampp\\mysql\\bin\\mysqldump.exe')) {
    $mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
}

$cmd = sprintf(
    '"%s" --host=%s --user=%s %s %s',
    $mysqldump_path,
    escapeshellarg($db_host),
    escapeshellarg($db_user),
    (!empty($db_pass) ? '--password=' . escapeshellarg($db_pass) : ''),
    escapeshellarg($db_name)
);

$output = [];$return_var = -1;
@exec($cmd, $output,$return_var);

if ($return_var === 0 && !empty($output)) {
    $content = implode("\n", $output);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $backup_filename . '"');
    header('Content-Length: ' . strlen($content));
    echo $content;
    exit;
} else {
    // Method 2: Pure PHP SQL Exporter Fallback
    try {
        $tables = [];
        $stmt =$pdo->query("SHOW TABLES");
        while ($row =$stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] =$row[0];
        }

        $sql_dump  = "-- Nyirefami Store Management System - Database Backup\n";
        $sql_dump .= "-- Database: " . $db_name . "\n";
        $sql_dump .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as$table) {
            $create_stmt =$pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
            $sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql_dump .=$create_stmt['Create Table'] . ";\n\n";

            $rows_stmt =$pdo->query("SELECT * FROM `$table`");
            while ($row =$rows_stmt->fetch(PDO::FETCH_ASSOC)) {
                $keys = array_map(function($k) { return "`$k`"; }, array_keys($row));$values = array_map(function($v) use ($pdo) {
                    return ($v === null) ? 'NULL' : $pdo->quote($v);
                }, array_values($row));

                $sql_dump .= "INSERT INTO `$table` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql_dump .= "\n";
        }

        $sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $backup_filename . '"');
        header('Content-Length: ' . strlen($sql_dump));
        echo $sql_dump;
        exit;

    } catch (Exception $e) {
        die("Backup Error: " . $e->getMessage());
    }
}
?>