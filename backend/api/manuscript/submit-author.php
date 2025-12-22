<?php
session_start();
require_once '../../lib/Database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'author') {
    die("Unauthorized access");
}

$root = $_SERVER['DOCUMENT_ROOT']; 
$authorId = $_SESSION['user']['UserID'];
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? null;
$genreName = $_POST['genre'] ?? null;

if (!$title || !$description || !$price || !$genreName || !isset($_FILES['cover']) || !isset($_FILES['manuscript'])) {
    die("All fields are required");
}

$sanitizedTitle = strtolower(trim($title));
$sanitizedTitle = preg_replace('/[^a-z0-9\-]+/', '', $sanitizedTitle);
$sanitizedTitle = str_replace(' ', '-', $sanitizedTitle);
$sanitizedTitle = trim($sanitizedTitle, '-');

$coverUploadDir = $root . '/yolobard/uploads/covers/';
$fileUploadDir = $root . '/yolobard/uploads/manuscripts/';

$coverBaseUrl = '/yolobard/uploads/covers/';
$fileBaseUrl = '/yolobard/uploads/manuscripts/';

if (!is_dir($coverUploadDir)) mkdir($coverUploadDir, 0755, true);
if (!is_dir($fileUploadDir)) mkdir($fileUploadDir, 0755, true);

$coverExtension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
$manuscriptExtension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);

$coverName = $sanitizedTitle . '_cover.' . $coverExtension;
$coverPath = $coverUploadDir . $coverName;
$manuscriptName = $sanitizedTitle . '_manuscript.' . $manuscriptExtension;
$manuscriptPath = $fileUploadDir . $manuscriptName;

if (!move_uploaded_file($_FILES['cover']['tmp_name'], $coverPath)) die("Cover upload failed");
if (!move_uploaded_file($_FILES['manuscript']['tmp_name'], $manuscriptPath)) die("Manuscript upload failed");

$coverBaseUrl .= $coverName;
$fileBaseUrl .= $manuscriptName;

$db = Database::getInstance()->getConnection();

$sql = "SELECT GenreID FROM Genres WHERE GenreName = ?";
$stmt = $db->prepare($sql);
if (!$stmt) die("Prepare failed: " . $db->error);
$stmt->bind_param("s", $genreName);
$stmt->execute();
$result = $stmt->get_result();
$genre = $result->fetch_assoc();
$stmt->close();

if (!$genre) {
    die("No Genre found with that name in the database");
}
$genreId = $genre['GenreID'];

$sql = "CALL sp_SubmitManuscript(?, ?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
if (!$stmt) die("Prepare failed: " . $db->error);

$stmt->bind_param("siisdss", $title, $authorId, $genreId, $description, $price, $fileBaseUrl, $coverBaseUrl);

if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

$stmt->close();

echo "<script>alert('Manuscript is successfully submitted for review!'); window.location.href='../../../frontend/author-page.php?success=1'</script>";
exit;
?>
