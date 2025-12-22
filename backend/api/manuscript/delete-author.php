<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'author') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit;
}

require_once '../../lib/Database.php';

$db = Database::getInstance()->getConnection();
$authorId = $_SESSION['user']['UserID'];

$manuscriptId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($manuscriptId <= 0) {
    http_response_code(400);
    echo "Invalid manuscript ID.";
    exit;
}

$checkStmt = $db->prepare("SELECT ManuscriptID, FileURL, CoverImageURL, Status 
                           FROM Manuscripts 
                           WHERE ManuscriptID = ? AND AuthorID = ?");
$checkStmt->bind_param("ii", $manuscriptId, $authorId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo "Manuscript not found or access denied.";
    exit;
}

$urlRow = $result->fetch_assoc();
$fileURL = "C:\\xampp\\htdocs" . $urlRow['FileURL'];
$coverURL = "C:\\xampp\\htdocs" . $urlRow['CoverImageURL'];
$status = $urlRow['Status'];

if (!file_exists($fileURL) || !file_exists($coverURL)) {
    die("Cover or manuscript file does not exist.");
}

if ($status !== 'Approved') {
    unlink($fileURL);
    unlink($coverURL);
}

$deleteStmt = $db->prepare("DELETE FROM Manuscripts WHERE ManuscriptID = ?");
$deleteStmt->bind_param("i", $manuscriptId);
if ($deleteStmt->execute()) {
    header("Location: ../../../frontend/author-page.php?deleted=1");
    exit;
} else {
    echo "Error deleting manuscript: " . $deleteStmt->error;
}
?>
