<?php
require_once '../../lib/Database.php';

$db = Database::getInstance()->getConnection(); 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $manuscriptId = $_POST['manuscript_id'];
    $editorId = $_POST['editor_id'];
    $feedback = $_POST['feedback'] ?? null;
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sql = "CALL sp_ApproveManuscript(?, ?, ?)";
    } elseif ($action === 'reject') {
        $sql = "CALL sp_RejectManuscript(?, ?, ?)";
    } else {
        die("Invalid action.");
    }

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }

    $stmt->bind_param("iis", $manuscriptId, $editorId, $feedback);

    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    $stmt->close();

    header("Location: ../../../frontend/editor-page.php");
    exit;
}
?>
