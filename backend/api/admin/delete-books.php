<?php
session_start();
require_once("../../lib/Database.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    die("Unauthorized access");
}

$bookId = (int)($_GET['book_id'] ?? 0);
if ($bookId <= 0) die("Invalid book ID");

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("CALL sp_DeleteBook(?)");
$stmt->bind_param("i", $bookId);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: /yolobard/frontend/admin-page.php");
    exit;
} else {
    die("Failed to delete the book: " . $db->error);
}
?>
