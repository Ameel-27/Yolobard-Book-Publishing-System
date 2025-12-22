<?php
session_start();
require_once '../../lib/Database.php';

$bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

if (!isset($_SESSION['user']['UserID']) || !$_SESSION['user']['UserID']) {
    echo "<script>alert('You must be logged in to rate a book!'); window.location.href='../../../frontend/book-details.php?id={$bookId}';</script>";
    exit();
}

$userId = (int)$_SESSION['user']['UserID'];
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$comment = trim($_POST['comment'] ?? '');

if ($bookId <= 0 || $rating < 1 || $rating > 5) {
    die('Invalid data.');
}

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("SELECT ReviewID FROM Reviews WHERE BookID = ? AND UserID = ?");
if (!$stmt) die("Prepare failed: " . $db->error);
$stmt->bind_param('ii', $bookId, $userId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $message = "You have already rated this book.";
} else {
    $stmt->close();

    $stmt = $db->prepare("INSERT INTO Reviews (BookID, UserID, Rating, Comment) VALUES (?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed: " . $db->error);

    $commentParam = $comment ?: null;
    $stmt->bind_param('iiis', $bookId, $userId, $rating, $commentParam);

    if ($stmt->execute()) {
        $message = "Your rating has been added.";
    } else {
        die("Insert failed: " . $stmt->error);
    }

    $stmt->close();
}

header("Location: ../../../frontend/book-details.php?id=$bookId");
exit();
