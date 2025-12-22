<?php
// Only allow access if the user is an editor
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'editor') {
    echo "<p>Unauthorized access.</p>";
    exit;
}

require_once '../backend/lib/Database.php';
$db = Database::getInstance()->getConnection();

// Prepare statement
$sql = "
    SELECT m.*, CONCAT(u.FirstName, ' ', u.LastName) AS AuthorName, g.GenreName
    FROM Manuscripts m
    JOIN Users u ON m.AuthorID = u.UserID
    JOIN Genres g ON m.GenreID = g.GenreID
    ORDER BY m.SubmittedAt DESC
";
$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "<p>Error retrieving manuscripts.</p>";
    exit;
}

while ($row = $result->fetch_assoc()) {
    $status = strtolower($row['Status']);
    $date = $row['SubmittedAt'] ? date('Y-m-d H:i:s', strtotime($row['SubmittedAt'])) : 'N/A';

    if ($status === 'pending') {
        $badge = "
        <form method='POST' action='../backend/api/manuscript/review-editor.php'>
            <input type='hidden' name='manuscript_id' value='{$row['ManuscriptID']}'>
            <input type='hidden' name='editor_id' value='{$_SESSION['user']['UserID']}'>
            <div class='review-actions'>
                <button type='submit' name='action' value='approve' class='button approve-btn'>Approve</button>
                <button type='submit' name='action' value='reject' class='button reject-btn'>Reject</button>
            </div>
            <textarea name='feedback' class='feedback' placeholder='Feedback for author (optional)'></textarea>
        </form>";
    } elseif ($status === 'approved') {
        $badge = "<div class='status-badge approved'>Approved</div>";
    } elseif ($status === 'rejected') {
        $badge = "<div class='status-badge rejected'>Rejected</div>
                  <div class='rejection-reason'><strong>Feedback:</strong> " . htmlspecialchars($row['Feedback']) . "</div>";
    }

    echo "<div class='manuscript' 
            data-title='" . strtolower(htmlspecialchars($row['Title'])) . "'
            data-author='" . strtolower(htmlspecialchars($row['AuthorName'])) . "'
            data-genre='" . strtolower(htmlspecialchars($row['GenreName'])) . "'
            data-status='{$status}'
            data-date='{$date}' >
            <div class='manuscript-info'>
              <div class='manuscript-meta'>
                <span class='manuscript-id'>#MS-{$row['ManuscriptID']}</span>
                <span class='submission-date'>Submitted: {$date}</span>
              </div>
              <h3 class='manuscript-title'>" . htmlspecialchars($row['Title']) . "</h3>
              <div class='manuscript-details'>
                <span class='author'>by " . htmlspecialchars($row['AuthorName']) . "</span>
                <span class='genre'>" . htmlspecialchars($row['GenreName']) . "</span>
              </div>
              <p class='manuscript-description'>" . htmlspecialchars($row['Description']) . "</p>
            </div>
            <div class='manuscript-actions'>
              <a href='" . htmlspecialchars($row['FileURL']) . "' class='button download-btn' download>Download Manuscript</a>
              {$badge}
            </div>
          </div>";
}
?>
