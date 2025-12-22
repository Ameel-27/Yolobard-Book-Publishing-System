<?php
$orderBy = 'b.BookID DESC';

switch ($sortOption) {
    case 'recent':
        $orderBy = 'b.BookID DESC';
        break;
    case 'bestsellers':
        $orderBy = 'QuantitySold DESC';
        break;
    case 'low-rated':
        $orderBy = 'AvgRating ASC';
        break;
    case 'high-rated':
        $orderBy = 'AvgRating DESC';
        break;
}

$bookPage = isset($_GET['book_page']) ? (int)$_GET['book_page'] : 1;
$bookLimit = 3;
$bookOffset = ($bookPage - 1) * $bookLimit;

$bookCountSql = "SELECT COUNT(*) AS TotalBooks FROM Books";
$result = $db->query($bookCountSql);
$totalBooks = $result->fetch_assoc()['TotalBooks'];
$totalBookPages = ceil($totalBooks / $bookLimit);

$params = [];
$types = "";

$sql = "
    SELECT 
        b.BookID, 
        b.Title, 
        CONCAT(u.FirstName, ' ', u.LastName) AS AuthorName,
        b.Price, 
        b.CoverImageURL, 
        IFNULL(sold.TotalSold, 0) AS QuantitySold,
        IFNULL(ROUND(avgReviews.AvgRating, 1), 0.0) AS AvgRating
    FROM Books b
    JOIN Users u ON b.AuthorID = u.UserID
    LEFT JOIN (
        SELECT BookID, SUM(QuantitySold) AS TotalSold
        FROM Sales
        GROUP BY BookID
    ) sold ON sold.BookID = b.BookID
    LEFT JOIN (
        SELECT BookID, AVG(Rating) AS AvgRating
        FROM Reviews
        GROUP BY BookID
    ) avgReviews ON avgReviews.BookID = b.BookID
    WHERE b.IsActive = 1
";

if (!empty($bookSearch)) {
    $sql .= " AND (b.Title LIKE ? OR CONCAT(u.FirstName, ' ', u.LastName) LIKE ?)";
    $searchTerm = '%' . $bookSearch . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

$sql .= " GROUP BY b.BookID, b.Title, u.FirstName, u.LastName, b.Price, b.CoverImageURL, QuantitySold";

$sql .= " ORDER BY $orderBy LIMIT ?, ?";
$params[] = $bookOffset;
$params[] = $bookLimit;
$types .= "ii";

$stmt = $db->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
?>
