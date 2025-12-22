<?php
session_start();
require_once '../backend/lib/Database.php';

if (!isset($_SESSION['user']['CartID'])) {
    die("You must be logged in to access a cart");
}

$db = Database::getInstance()->getConnection();
$cartID = $_SESSION['user']['CartID'];

// Prepare statement to get cart items with book info
$stmt = $db->prepare("
    SELECT 
        b.BookID,
        b.Title,
        CONCAT(u.FirstName, ' ', u.LastName) AS AuthorName,
        g.GenreName,
        b.Price,
        b.CoverImageURL,
        c.Quantity
    FROM Cart_Items c
    JOIN Books b ON c.BookID = b.BookID
    JOIN Users u ON b.AuthorID = u.UserID
    JOIN Genres g ON b.GenreID = g.GenreID
    WHERE c.CartID = ?
");
$stmt->bind_param("i", $cartID);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart - Yolo-Bard</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/shopping-cart.css">
  <script src="./js/index.js" defer></script>
</head>
<body>
  <nav>
    <div class="nav-brand">
      <a href="./index.html">Yolo-Bard</a>
    </div>
    <div id="nav-button">&equiv;</div>
    <?php include('../backend/api/auth/navbar.php'); ?>
  </nav>

  <main class="cart-container">
      <h1>Your Shopping Cart</h1>
      
      <div class="cart-content">
        <div class="cart-items">
          <div class="cart-header">
            <span class="item-col">Item</span>
            <span class="price-col">Price</span>
            <span class="qty-col">Quantity</span>
            <span class="total-col">Total</span>
            <span class="action-col">Action</span>
          </div>
        
          <?php $total = 0; ?>
          <?php foreach($cartItems as $items):
            $total += $items['Price'] * $items['Quantity'];
          ?>
          <div class="cart-item">
            <div class="item-info">
              <img src="<?= htmlspecialchars($items['CoverImageURL']) ?>" alt="<?= htmlspecialchars($items['Title']) ?>" class="book-cover">
              <div class="item-details">
                <h3><?= htmlspecialchars($items['Title']) ?></h3>
                <p>by <?= htmlspecialchars($items['AuthorName']) ?></p>
                <span class="genre"><?= htmlspecialchars($items['GenreName']) ?></span>
              </div>
            </div>
            <div class="item-price">$<?= number_format($items['Price'], 2) ?></div>
            <div class="item-qty">
              <?php if ($items['Quantity'] > 1): ?>
                <a href="../backend/api/cart/decrease-quantity.php?book_id=<?= $items['BookID'] ?>" class="qty-btn">-</a>
              <?php else: ?>
                <a class="qty-btn disabled" style="pointer-events: none; opacity: 0.5;">-</a>
              <?php endif; ?>
              <span><?= $items['Quantity'] ?></span>
              <a href="../backend/api/cart/increase-quantity.php?book_id=<?= $items['BookID'] ?>" class="qty-btn">+</a>
            </div>
            <div class="item-total">$<?= number_format($items['Price'] * $items['Quantity'], 2) ?></div>
            <form action="../backend/api/cart/remove-from-cart.php" method="POST" class="item-action">
              <input type="hidden" name="book_id" value="<?= $items['BookID'] ?>">
              <button class="button danger remove-btn">Remove</button>
            </form>
          </div>
          <?php endforeach; ?>
        </div> 
      
      <div class="cart-summary">
        <h3>Order Summary</h3>
        <div class="summary-row">
          <span>Subtotal</span>
          <span>$<?= number_format($total, 2) ?></span>
        </div>
        <div class="summary-row">
          <span>Shipping</span>
          <span>Free</span>
        </div>
        <div class="summary-row total">
          <span>Total</span>
          <span>$<?= number_format($total, 2) ?></span>
        </div>
        <?php if(!empty($cartItems)): ?>
          <a href="../backend/api/cart/checkout.php" class="button success checkout-btn"
          onclick="return confirm('Are you sure you want to checkout?');">Proceed to Checkout</a>
        <?php else: ?>
          <a class="button success checkout-btn" id="disabled-checkout-btn" disabled>Proceed to Checkout</a>
        <?php endif; ?>
        <a href="./bookstore.php" class="continue-shopping">Continue Shopping</a>
      </div>
    </div>
  </main>
</body>
</html>
