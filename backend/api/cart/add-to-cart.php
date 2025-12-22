<?php
session_start();
require_once '../../lib/Database.php';

if (!isset($_SESSION['user']['CartID'])) {
    die('You must be logged in to add items to cart.');
}

$cartId = (int)$_SESSION['user']['CartID'];
$bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

if ($bookId <= 0) {
    die('Invalid book.');
}

$db = Database::getInstance()->getConnection();

/* -------------------------------------------
   CHECK IF ITEM IS ALREADY IN CART
-------------------------------------------- */
$checkSql = "SELECT Quantity FROM Cart_Items WHERE CartID = ? AND BookID = ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("ii", $cartId, $bookId);
$checkStmt->execute();
$result = $checkStmt->get_result();
$existing = $result->fetch_assoc();
$checkStmt->close();

/* -------------------------------------------
   INSERT ONLY IF NOT ALREADY IN CART
-------------------------------------------- */
if (!$existing) {
    $insertSql = "INSERT INTO Cart_Items (CartID, BookID) VALUES (?, ?)";
    $insertStmt = $db->prepare($insertSql);
    $insertStmt->bind_param("ii", $cartId, $bookId);
    $insertStmt->execute();
    $insertStmt->close();
}

// Redirect back to the originating page
$redirectPage = isset($_POST['redirect']) 
    ? $_POST['redirect'] 
    : "/yolobard/frontend/book-details.php?id=$bookId";

header("Location: $redirectPage");
exit;
