<?php
session_start();
require_once("../../lib/Database.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    http_response_code(403);
    die("Unauthorized access");
}

$userId = (int) ($_GET['user_id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($userId <= 0 || !in_array($action, ['assign', 'remove'])) {
    http_response_code(400);
    die("Invalid input");
}

// Get MySQLi connection
$db = Database::getInstance()->getConnection();

$procedure = '';
if ($action === 'assign') {
    $procedure = 'sp_AssignEditorRole';
} else if ($action === 'remove') {
    $procedure = 'sp_RemoveEditorRole';
}

// Prepare and execute stored procedure
$stmt = $db->prepare("CALL $procedure(?)");
if (!$stmt) {
    http_response_code(500);
    die("Failed to prepare statement: " . $db->error);
}

$stmt->bind_param("i", $userId);

if (!$stmt->execute()) {
    http_response_code(500);
    die("Failed to execute stored procedure: " . $stmt->error);
}

$stmt->close();
$db->close();

header("Location: /yolobard/frontend/admin-page.php");
exit;
