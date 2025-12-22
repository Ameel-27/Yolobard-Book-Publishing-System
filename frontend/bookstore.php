<?php
session_start();
require_once('../backend/lib/Database.php');
$db = Database::getInstance()->getConnection();

// Retrieve ids of all books from the cart
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

// Get filters from GET
$genreFilter = $_GET['genre'] ?? '';
$searchQuery = $_GET['search'] ?? '';
$sortOption  = $_GET['sort'] ?? 'newest';

// Base query
$sql = "
    SELECT
        b.*,
        CONCAT(u.FirstName, ' ', u.LastName) AS AuthorName,
        g.GenreName,
        IFNULL(ROUND(AVG(r.Rating),1),0) AS AvgRating
    FROM Books b
    JOIN Users u ON b.AuthorID = u.UserID
    JOIN Genres g ON b.GenreID = g.GenreID
    LEFT JOIN Reviews r ON b.BookID = r.BookID
    WHERE b.IsActive = 1
";

// Parameters array
$params = [];
$types = "";

// Genre filter
if (!empty($genreFilter)) {
    $sql .= " AND g.GenreName = ?";
    $params[] = $genreFilter;
    $types .= "s";
}

// Search filter
if (!empty($searchQuery)) {
    $sql .= " AND (b.Title LIKE ? OR CONCAT(u.FirstName, ' ', u.LastName) LIKE ?)";
    $searchTerm = '%' . $searchQuery . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Group by
$sql .= " GROUP BY b.BookID, b.Title, b.AuthorID, b.GenreID, b.Description, b.Price, b.CoverImageURL, b.CreatedAt, u.FirstName, u.LastName, g.GenreName";

// Sorting
switch ($sortOption) {
    case 'price_asc':
        $sql .= " ORDER BY b.Price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY b.Price DESC";
        break;
    case 'popular':
        $sql .= " ORDER BY AvgRating DESC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY b.CreatedAt DESC";
        break;
}

$stmt = $db->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Yolo-Bard &ndash; Book Store</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/bookstore.css">
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
    <aside id="categories">
      <h2>Genres</h2>
      <ul>
        <li><a href="?genre=Fiction">Fiction</a></li>
        <li><a href="?genre=Horror">Horror</a></li>
        <li><a href="?genre=Children's Books">Children's Books</a></li>
        <li><a href="?genre=Mystery and Thriller">Mystery & Thriller</a></li>
        <li><a href="?genre=Romance">Romance</a></li>
        <li><a href="?genre=Academic and Educational">Academic & Educational</a></li>
        <li><a href="?genre=Business and Finance">Business & Finance</a></li>
        <li><a href="?genre=True Crime">True Crime</a></li>
        <li><a href="?genre=Drama and Plays">Drama & Plays</a></li>
        <li><a href="?genre=Religious and Spiritual">Religious & Spiritual</a></li>
      </ul>
      <br>
      <a href="?" class="button">Reset Filters</a>
      <div class="cart-option">
        <img src="./assets/cart-icon.png" alt="cart icon" style="margin-left: 30%;">
        <a href="./shopping-cart.php" class="button">Go to cart</a>
      </div>
    </aside>

    <section id="books-wrapper">
      <h2>Featured Books</h2>
      <form method="get" id="books-controls">
        <input type="text" name="search" placeholder="Search for title or author" value="<?= htmlspecialchars($searchQuery) ?>">
        <select name="sort" onchange="this.form.submit()">
          <option value="newest" <?= $sortOption == 'newest' ? 'selected' : '' ?>>Latest releases</option>
          <option value="popular" <?= $sortOption == 'popular' ? 'selected' : '' ?>>Most popular</option>
          <option value="price_asc" <?= $sortOption == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price_desc" <?= $sortOption == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
        </select>
        <button type="submit">Search</button>
      </form>

      <div id="books">
        <?php foreach ($books as $book): ?>
          <div class="book">
            <img 
              src="<?= htmlspecialchars($book['CoverImageURL']) ?>" 
              alt="Book Cover" 
              class="book-thumbnail"
              width="320"
              height="240"
            >
            <form method="post" action="../backend/api/cart/add-to-cart.php" class="book-details">
              <h3 class="book-name"><?= htmlspecialchars($book['Title']) ?></h3>
              <span class="book-price">$<?= number_format($book['Price'], 2) ?></span>
              <p>by <?= htmlspecialchars($book['AuthorName']) ?></p>
              <input type="hidden" name="book_id" value="<?= $book['BookID'] ?>">
              <input type="hidden" name="redirect" value="/yolobard/frontend/bookstore.php">
              <?php if (isset($_SESSION['user']) && in_array($book['BookID'], $cartItems )): ?>
                <button class="book-button success" disabled>Added</button>
              <?php else: ?>
                <button class="book-button success">Add to Cart</button>
              <?php endif; ?>
              <a href="book-details.php?id=<?= $book['BookID'] ?>" class="book-button button">View Details</a>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
