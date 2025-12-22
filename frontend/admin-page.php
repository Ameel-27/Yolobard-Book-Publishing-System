<?php
session_start();
require_once "../backend/lib/Database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    die("Unauthorized access");
}

$db = Database::getInstance()->getConnection();

$sortOption = $_GET['sort'] ?? 'all';
$bookSearch = $_GET['book_search'] ?? '';
$userSearch = $_GET['user_search'] ?? '';

// Retrieve statistics (total books, weekly sales etc)
include('../backend/api/admin/retrieveStatistics.php');


// PAGINATION FOR ROLE MANAGEMENT
$page = (int)($_GET['page'] ?? 1);
$limit = 3;
$offset = ($page - 1) * $limit;

// Count non-admin users
$countSql = "SELECT COUNT(*) AS Total FROM Users WHERE Role <> 'admin'";
$countResult = $db->query($countSql);
$totalUsers = $countResult->fetch_assoc()['Total'];
$totalPages = ceil($totalUsers / $limit);


// FETCH USERS
$userSql = "
    SELECT 
        UserID,
        CONCAT(FirstName, ' ', LastName) AS FullName,
        Email,
        Role,
        CONCAT(LEFT(FirstName,1), LEFT(LastName,1)) AS NameID
    FROM Users
    WHERE Role <> 'admin'
";

$params = [];
$types = "";

// Search
if (!empty($userSearch)) {
    $userSql .= " AND (CONCAT(FirstName,' ',LastName) LIKE ? OR Email LIKE ?)";
    $searchTerm = "%$userSearch%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Add LIMIT/OFFSET
$userSql .= " ORDER BY FirstName LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt = $db->prepare($userSql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}


// SALES ANALYTICS
$startOfWeek = date("Y-m-d 00:00:00", strtotime("monday this week"));
$endOfWeek   = date("Y-m-d 23:59:59", strtotime("sunday this week"));


// TOP 3 BOOKS SOLD THIS WEEK
$sql = "
    SELECT 
        b.Title,
        CONCAT(u.FirstName, ' ', u.LastName) AS Author,
        SUM(s.QuantitySold) AS Copies,
        SUM(s.QuantitySold * s.SalePrice) AS Revenue
    FROM Sales s
    JOIN Books b ON s.BookID = b.BookID
    JOIN Users u ON s.AuthorID = u.UserID
    WHERE s.SaleDate BETWEEN ? AND ?
    GROUP BY b.Title, u.FirstName, u.LastName
    ORDER BY Copies DESC
    LIMIT 3
";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $startOfWeek, $endOfWeek);
$stmt->execute();
$result = $stmt->get_result();

$topSellingBooksThisWeek = [];
while ($row = $result->fetch_assoc()) {
    $topSellingBooksThisWeek[] = $row;
}

// WEEKLY SALES WITH ROLLUP
$weeklyBreakdownSql = "
    SELECT 
        DATE(SaleDate) AS SaleDay,
        SUM(SalePrice * QuantitySold) AS TotalSales
    FROM Sales
    WHERE SaleDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY SaleDay WITH ROLLUP
";

$result = $db->query($weeklyBreakdownSql);

$weeklyBreakdown = [];
while ($row = $result->fetch_assoc()) {
    $weeklyBreakdown[] = [
        "date"  => $row["SaleDay"] ?? "TOTAL",
        "sales" => $row["TotalSales"] ?? 0
    ];
}


// AUTHOR SALES 
$authorSalesSql = "
    SELECT 
        CONCAT(u.FirstName, ' ', u.LastName) AS AuthorName,
        b.Title,
        SUM(s.SalePrice * s.QuantitySold) AS Revenue
    FROM Sales s
    JOIN Books b ON b.BookID = s.BookID
    JOIN Users u ON u.UserID = b.AuthorID
    GROUP BY AuthorName, b.Title WITH ROLLUP
";

$result = $db->query($authorSalesSql);

$authorSales = [];
while ($row = $result->fetch_assoc()) {
    $authorSales[] = [
        'author'  => $row['AuthorName'] ?? 'TOTAL',
        'title'   => $row['Title'] ?? 'ALL BOOKS',
        'revenue' => $row['Revenue'] ?? 0
    ];
}




// BOOK MANAGEMENT
include('../backend/api/admin/book-management.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Yolo-Bard</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/admin-page.css">
  <script src="./js/index.js" defer></script>
  <script src="./js/admin.js" defer></script>
</head>
<body>
  <nav>
    <div class="nav-brand">
      <a href="./index.html">Yolo-Bard</a>
    </div>
    <div id="nav-button">&equiv;</div>
    <?php include('../backend/api/auth/navbar.php'); ?>
  </nav>
  <main class="admin-container">
    <div class="admin-overview">
      <div class="admin-card primary">
        <h3>Total Books</h3>
        <div class="stat"><?= $totalBooks ?></div>
      </div>

      <div class="admin-card success">
        <h3>Weekly Sales</h3>
        <div class="stat">$<?= $weeklySales ?></div>
      </div>
      
      <div class="admin-card warning">
        <h3>Pending Reviews</h3>
        <div class="stat"><?= $reviews ?></div>
      </div>
      
      <div class="admin-card danger">
        <h3>Active Authors</h3>
        <div class="stat"><?= $activeAuthors ?></div>
      </div>
    </div>

    <!-- Main Admin Content -->
    <div class="admin-content">
      <!-- Role Management Section -->
      <section class="admin-section role-management">
        <h2><i class="icon">👑</i> Role Management</h2>
        <form method="GET" class="search-controls">
          <input type="text" name="user_search" placeholder="Search users by name" value="<?= $_GET['user_search'] ?? '' ?>">
          <button type="submit" class="button">Search</button>
        </form>
        
        <div class="user-list">
          <?php foreach($users as $user): ?>
          <div class="user-card">
            <div class="user-info">
              <div class="avatar"><?= $user['NameID'] ?></div>
              <div class="user-details">
                <h4><?= $user['FullName'] ?></h4>
                <p><?= $user['Email'] ?></p>
                <div class="user-roles">
                  <span class="role-tag <?= $user['Role'] ?>"><?= $user['Role'] ?></span>
                </div>
              </div>
            </div>
            <div class="role-actions">
              <?php if($user['Role'] === 'editor'): ?>
                <a href="../backend/api/admin/toggle-editor.php?user_id=<?= $user['UserID'] ?>&action=remove" class="button danger">Remove Editor</a>
              <?php else: ?>
                <a href="../backend/api/admin/toggle-editor.php?user_id=<?= $user['UserID'] ?>&action=assign" class="button success">Make Editor</a>
              <?php endif; ?>
            </div>
            
          </div>
          <?php endforeach; ?>
        </div>
        <div class="pagination" style="text-align: center;">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>" class="button">Prev</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>" class="button <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page + 1 ?>" class="button">Next</a>
        <?php endif; ?>
      </div>
      </section>

      <!-- Sales Analytics Section -->
      <section class="admin-section analytics">
        <h2><i class="icon">📊</i> Sales Analytics</h2>

        <div class="analytics-grid">
          <div class="top-performers">
            <h3>Top Selling Books This Week</h3>

            <?php if (!empty($topSellingBooksThisWeek)): ?>
              <?php foreach ($topSellingBooksThisWeek as $index => $book): ?>
                <div class="performer-card">
                  <div class="performer-header">
                    <span class="rank"><?= $index + 1 ?></span>
                    <h4><?= htmlspecialchars($book['Title']) ?></h4>
                    <span class="sales">
                      $<?= number_format($book['Revenue'], 2) ?>
                    </span>
                  </div>
                  <p>
                    By <?= htmlspecialchars($book['Author']) ?> • <?= (int) $book['Copies'] ?> copies sold
                  </p>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>No sales data available for this week.</p>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <div class="sales-tables" style="display: flex; flex-wrap: wrap; gap: 2rem; margin-top: 2rem;">
        <!-- Weekly Sales Summary -->
        <div class="admin-card" style="flex: 1; min-width: 300px;">
          <h2>📅 Weekly Sales</h2>
          <table class="mini-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Total ($)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($weeklyBreakdown as $entry): ?>
                <tr>
                  <td><?= htmlspecialchars($entry['date']) ?></td>
                  <td>$<?= number_format($entry['sales'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      
        <!-- Author Sales Summary -->
        <div class="admin-card" style="flex: 2; min-width: 400px;">
          <h2>📚 Book Sales by Author</h2>
          <table class="mini-table">
            <thead>
              <tr>
                <th>Author</th>
                <th>Title</th>
                <th>Revenue ($)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($authorSales as $entry): ?>
                <tr>
                  <td><?= htmlspecialchars($entry['author']) ?></td>
                  <td><?= htmlspecialchars($entry['title']) ?></td>
                  <td>$<?= number_format($entry['revenue'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      
      </div>


      <!-- Book Management Section -->
      <section class="admin-section book-management">
        <h2><i class="icon">📚</i> Book Management</h2>
        <form method="GET" class="book-controls">
          <input type="text" name="book_search" placeholder="Search books by title or author" value="<?= $_GET['book_search'] ?? '' ?>">
          <select name="sort" onchange="this.form.submit()">
            <option value="all" <?= ($sortOption === 'all') ? 'selected' : '' ?>>All Books</option>
            <option value="recent" <?= ($sortOption === 'recent') ? 'selected' : '' ?>>Recently Added</option>
            <option value="bestsellers" <?= ($sortOption === 'bestsellers') ? 'selected' : '' ?>>Bestsellers</option>
            <option value="low-rated" <?= ($sortOption === 'low-rated') ? 'selected' : '' ?>>Low Rated</option>
            <option value="high-rated" <?= ($sortOption === 'high-rated') ? 'selected' : '' ?>>High Rated</option>
          </select>
          <button type="submit" class="button">Search</button>
        </form>
        <div class="book-list">
          <?php foreach($books as $book): ?>
          <div class="book-card">
              <img src="<?= $book['CoverImageURL'] ?>" class="book-cover">
            <div class="book-details">
              <h4><?= $book['Title'] ?></h4>
              <p>By <?= $book['AuthorName'] ?></p>
              <div class="book-meta">
                <span class="rating"><?= $book['AvgRating'] ?></span>
                <span class="price">$<?= $book['Price'] ?></span>
                <span class="sales"><?= $book['QuantitySold'] ?> sales</span>
              </div>
            </div>
            <div class="book-actions">
              <a href="../backend/api/admin/delete-books.php?book_id=<?= $book['BookID'] ?>" 
              class="button" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
            </div>
          </div>
          <?php endforeach; ?>
          <div class="pagination" style="text-align: center; margin-top: 1rem;">
            <?php if ($bookPage > 1): ?>
              <a href="?book_page=<?= $bookPage - 1 ?>" class="button">Prev</a>
            <?php endif; ?>
          
            <?php for ($i = 1; $i <= $totalBookPages; $i++): ?>
              <a href="?book_page=<?= $i ?>" class="button <?= $i == $bookPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
          
            <?php if ($bookPage < $totalBookPages): ?>
              <a href="?book_page=<?= $bookPage + 1 ?>" class="button">Next</a>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>
</html>