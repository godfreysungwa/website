<?php
// Enable full error reporting to catch hidden failures
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Starting User Creation Diagnostics...</h2>";

// 1. Locate db.php
$dbPath = null;
$possiblePaths = ['db.php', '../db.php', '../../db.php', '../../../db.php'];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $dbPath = $path;
        break;
    }
}

if (!$dbPath) {
    die("<p style='color:red;'><strong>FAILURE:</strong> Could not find <code>db.php</code> in any expected directory.</p>");
}

echo "<p style='color:blue;'>Found database connection file at: <code>" . realpath($dbPath) . "</code></p>";

require_once $dbPath;

if (!isset($pdo)) {
    die("<p style='color:red;'><strong>FAILURE:</strong> <code>\$pdo</code> variable is not initialized in <code>db.php</code>.</p>");
}

try {
    // 2. Drop existing table if corrupt and recreate clean table
    echo "<p>Checking <code>users</code> table structure...</p>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('Admin', 'StoreKeeper', 'Manager') NOT NULL DEFAULT 'StoreKeeper',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "<p style='color:green;'>Table <code>users</code> is ready.</p>";

    // 3. User Details
    $my_username = 'admin';
    $my_password = '766Freys$';
    $my_fullname = 'Sungwa';
    $my_role     = 'Admin';

    $hashed_password = password_hash($my_password, PASSWORD_DEFAULT);

    // 4. Force Delete existing 'admin' to prevent unique key conflict issues
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE LOWER(username) = LOWER(:username)");
    $deleteStmt->execute([':username' => $my_username]);

    // 5. Insert Fresh Record
    $insertStmt = $pdo->prepare("
        INSERT INTO users (username, password_hash, full_name, role)
        VALUES (:username, :password_hash, :full_name, :role)
    ");

    $success = $insertStmt->execute([
        ':username'      => $my_username,
        ':password_hash' => $hashed_password,
        ':full_name'     => $my_fullname,
        ':role'          => $my_role
    ]);

    if ($success) {
        echo "<div style='padding: 15px; background: #d1fae5; border: 1px solid #059669; border-radius: 8px; color: #065f46;'>";
        echo "<h3>SUCCESS! Admin User Added to Database</h3>";
        echo "<p><strong>Username:</strong> " . htmlspecialchars($my_username) . "</p>";
        echo "<p><strong>Password:</strong> " . htmlspecialchars($my_password) . "</p>";
        echo "</div>";

        // Verify record directly from DB
        $checkStmt = $pdo->query("SELECT id, username, full_name, role FROM users");
        $allUsers = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3>Current Users in Table:</h3>";
        echo "<pre>" . print_r($allUsers, true) . "</pre>";
    } else {
        echo "<p style='color:red;'><strong>INSERT FAILED:</strong> Could not write row to database.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>DATABASE ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>