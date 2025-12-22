<?php
require_once '../../yolobard/backend/lib/Database.php';

$db = Database::getInstance()->getConnection();
$authorId = $_SESSION['user']['UserID'];

// Prepare statement
$stmt = $db->prepare("SELECT m.ManuscriptID, m.Title, g.GenreName, m.SubmittedAt, m.Status
                      FROM Manuscripts m
                      JOIN Genres g ON m.GenreID = g.GenreID
                      WHERE m.AuthorID = ?
                      ORDER BY m.SubmittedAt DESC");
$stmt->bind_param("i", $authorId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "<p class='error'>Error loading manuscripts.</p>";
    return;
}

if ($result->num_rows === 0) {
    echo "<h2>No manuscripts submitted</h2>";
}

while ($row = $result->fetch_assoc()):
    $submittedAt = $row['SubmittedAt'] ? date('d M Y', strtotime($row['SubmittedAt'])) : 'N/A';
?>
  <div class="manuscript-card">
    <div class="manuscript-info">
      <h3><?= htmlspecialchars($row['Title']) ?></h3>
      <div class="meta-info">
        <span class="genre"><?= htmlspecialchars($row['GenreName']) ?></span>
        <span class="date">
          Submitted: <?= $submittedAt ?>
        </span>
      </div>
      <div class="status <?= strtolower($row['Status']) ?>">
        <?= htmlspecialchars($row['Status']) ?>
      </div>
    </div>
    <div class="manuscript-actions">
      <a href="/yolobard/backend/api/manuscript/delete-author.php?id=<?= $row['ManuscriptID'] ?>" 
         onclick="return confirm('Are you sure you want to delete this manuscript?');"
         class="button delete-btn">
         Delete
      </a>
    </div>
  </div>
<?php endwhile; ?>
