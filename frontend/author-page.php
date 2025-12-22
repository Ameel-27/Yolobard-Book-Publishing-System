<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Author Dashboard - Yolo-Bard</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/author-page.css">
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

  <main class="dashboard-container">
    <div class="dashboard-header">
      <h1>Your Manuscripts</h1>
      <a href="./submission.php" class="new-submission-btn">
        + Submit New Manuscript
      </a>
    </div>
    
    <div class="manuscripts-list">
     <?php include('../backend/api/manuscript/display-author.php'); ?>
    </div>
  </main>
</body>
</html>