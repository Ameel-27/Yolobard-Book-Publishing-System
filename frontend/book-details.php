<?php
session_start();
require_once '../backend/lib/Database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid book ID.');
}

$bookId = (int)$_GET['id'];
$db = Database::getInstance()->getConnection();

// Retrieve ids of all books in the cart
$cartItems = [];
if (isset($_SESSION['user']['CartID'])) {
    $cartItems = [];
    $mycartID = $_SESSION['user']['CartID'];
    $stmt = $db->prepare("SELECT BookID FROM Cart_Items WHERE CartID = ?");
    $stmt->bind_param("i", $mycartID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row['BookID'];
    }
    $stmt->close();
}

// Fetch book details (with author and genre)
$sql = "
    SELECT b.*, CONCAT(u.FirstName, ' ', u.LastName) AS AuthorName, g.GenreName
    FROM Books b
    JOIN Users u ON b.AuthorID = u.UserID
    JOIN Genres g ON b.GenreID = g.GenreID
    WHERE b.BookID = ?
";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die('Book not found.');
}

// Fetch reviews
$reviewsSql = "
    SELECT r.Rating, r.Comment, r.CreatedAt, u.FirstName, u.LastName
    FROM Reviews r
    JOIN Users u ON r.UserID = u.UserID
    WHERE r.BookID = ?
    ORDER BY r.CreatedAt DESC
";
$stmt = $db->prepare($reviewsSql);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$reviewsResult = $stmt->get_result();
$reviews = $reviewsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch average rating
$ratingSql = "
    SELECT IFNULL(ROUND(AVG(Rating),1),0) AS AvgRating
    FROM Reviews
    WHERE BookID = ?
";
$stmt = $db->prepare($ratingSql);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();
$avgRatingRow = $result->fetch_assoc();
$avgRating = $avgRatingRow['AvgRating'] ?? 0;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($book['Title']) ?> &ndash; Book Details</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/bookstore.css">
  <link rel="stylesheet" href="./css/book-details.css">
</head>
<body>
  <nav>
    <div class="nav-brand">
      <a href="./index.html">Yolo-Bard</a>
    </div>
    <div id="nav-button">&equiv;</div>
    <?php include('../backend/api/auth/navbar.php'); ?>
  </nav>

  <main>
    <h1>Book Details</h1>
    <div class="book-details-page">
        <div class="book-container">
            <div>
                <img src="<?= htmlspecialchars($book['CoverImageURL']) ?>" alt="Cover Image" class="book-cover">
            </div>
            <div class="book-info">
                <h1><?= htmlspecialchars($book['Title']) ?></h1>
                <h3>by <?= htmlspecialchars($book['AuthorName']) ?></h3>
                <p><strong>Genre:</strong> <?= htmlspecialchars($book['GenreName']) ?></p>
                <p><strong>Price:</strong> $<?= number_format($book['Price'], 2) ?></p>
                <p><?= htmlspecialchars($book['Description']) ?></p>
                <form action="../backend/api/cart/add-to-cart.php" method="post" class="cart-buttons">
                    <input type="hidden" name="book_id" value="<?= $book['BookID'] ?>">
                    <?php if (isset($_SESSION['user']) && in_array($book['BookID'], $cartItems)): ?>
                    <button class="book-button success" disabled>Added</button>
                    <?php else: ?>
                    <button class="book-button success">Add to Cart</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="review-container">
            <div class="rating-section">
                <h2 style="margin-bottom: 2rem;">Average Rating: <?= $avgRating ?></h2>
                <h3>Rate this book</h3>
                <form action="../backend/api/book/rate-book.php" method="post" class="rating-form">
                    <input type="hidden" name="book_id" value="<?= $book['BookID'] ?>">
                    <label>Rating (1 to 5):
                        <input type="number" name="rating" min="1" max="5" required>
                    </label>
                    <textarea name="comment" placeholder="Comment (Optional)" rows="3"></textarea>
                    <button>Submit Rating</button>
                </form>
            </div>
        </div>
    </div>
    <div class="reviews">
        <h2>Reviews</h2>
        <?php foreach ($reviews as $review): ?>
        <div class="review">
            <strong><?= htmlspecialchars($review['FirstName'] . ' ' . $review['LastName']) ?></strong>
            <span>rated <?= $review['Rating'] ?>/5</span><br>
            <small><?= date('Y-m-d H:i', strtotime($review['CreatedAt'])) ?></small>
            <?php if (!empty($review['Comment'])): ?>
                <p><?= htmlspecialchars($review['Comment']) ?></p>
            <?php endif; ?>
            <hr>
        </div>
        <?php endforeach; ?>
    </div>
  </main>
</body>
</html>
