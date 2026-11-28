<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
    // Extend session cookie lifetime to 30 days (30 days * 24 hrs * 60 mins * 60 secs)
    ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
    ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
}
session_start();

$dbPath = null;
$possiblePaths = ['db.php', '../db.php', '../../db.php', '../../../db.php'];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $dbPath = $path;
        break;
    }
}

if (!$dbPath) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

require_once $dbPath;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in both username and password.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, role FROM users WHERE LOWER(username) = LOWER(:username) LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Set Session Variables
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            echo json_encode([
                'status'   => 'success',
                'message'  => 'Authentication successful.',
                'redirect' => 'homepage.html'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>