<?php
// ---------------------------------------------
// TOTAL BOOKS
// ---------------------------------------------
$totalBooks = 0;
$totalBooksSql = "SELECT COUNT(*) AS TotalBooks FROM Books";
$result = $db->query($totalBooksSql);
if ($row = $result->fetch_assoc()) {
    $totalBooks = $row['TotalBooks'] ?? 0;
}


// ---------------------------------------------
// WEEKLY SALES
// ---------------------------------------------
$weeklySales = 0;
$weeklySalesSql = "
    SELECT SUM(SalePrice * QuantitySold) AS TotalWeeklySales
    FROM Sales
    WHERE SaleDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
";

$result = $db->query($weeklySalesSql);
if ($row = $result->fetch_assoc()) {
    $weeklySales = $row['TotalWeeklySales'] ?? 0;
}


// ---------------------------------------------
// PENDING REVIEWS
// ---------------------------------------------
$reviews = 0;
$reviewsSql = "
    SELECT COUNT(*) AS PendingReviews
    FROM Manuscripts
    WHERE Status = 'Pending'
";

$result = $db->query($reviewsSql);
if ($row = $result->fetch_assoc()) {
    $reviews = $row['PendingReviews'] ?? 0;
}


// ---------------------------------------------
// ACTIVE AUTHORS (LAST 7 DAYS)
// ---------------------------------------------
$activeAuthors = 0;
$authorsSql = "
    SELECT COUNT(*) AS ActiveAuthors
    FROM Users
    WHERE Role = 'author'
      AND LastActive >= DATE_SUB(NOW(), INTERVAL 7 DAY)
";

$result = $db->query($authorsSql);
if ($row = $result->fetch_assoc()) {
    $activeAuthors = $row['ActiveAuthors'] ?? 0;
}
?>
