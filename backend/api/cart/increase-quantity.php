<?php
session_start();
require_once '../../lib/Database.php';

if (!isset($_GET['book_id'], $_SESSION['user']['CartID'])) {
    die("Invalid request.");
}

$bookId = (int) $_GET['book_id'];
$cartId = (int) $_SESSION['user']['CartID'];

$db = Database::getInstance()->getConnection();

$sql = "UPDATE Cart_Items 
        SET Quantity = Quantity + 1 
        WHERE CartID = ? AND BookID = ?";

$stmt = $db->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $db->error);
}

$stmt->bind_param("ii", $cartId, $bookId);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$stmt->close();

header("Location: /yolobard/frontend/shopping-cart.php");
exit;
