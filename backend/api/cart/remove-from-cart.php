<?php
session_start();
require_once '../../lib/Database.php';

if (!isset($_SESSION['user']['CartID'])) {
    die('You must be logged in to remove items from cart.');
}

$cartId = (int)$_SESSION['user']['CartID'];
$bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

if ($bookId <= 0) {
    die('Invalid book.');
}

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("DELETE FROM Cart_Items WHERE CartID = ? AND BookID = ?");
if (!$stmt) {
    die("Prepare failed: " . $db->error);
}

$stmt->bind_param("ii", $cartId, $bookId);

if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

$stmt->close();

header("Location: /yolobard/frontend/shopping-cart.php");
exit;
?>
