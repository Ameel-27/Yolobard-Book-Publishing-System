<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editor Dashboard - Yolo-Bard</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/editor-page.css">
  <script src="./js/index.js" defer></script>
  <script src="./js/editor-script.js" defer></script>
</head>
<body>
  <nav>
    <div class="nav-brand">
      <a href="./index.html">Yolo-Bard</a>
    </div>
    <div id="nav-button">&equiv;</div>
    <?php include('../backend/api/auth/navbar.php'); ?>
  </nav>
  <main class="editor-container">
    <section id="search-filters">
      <h2>Manuscript Review</h2>
      <div class="input-row">
        <input type="text" id="search-title" placeholder="Search by title">
        <input type="text" id="search-author" placeholder="Search by author">
        <select id="search-genre">
          <option value="">All Genres</option>
          <option value="fiction">Fiction</option>
          <option value="horror">Horror</option>
          <option value="children's books">Children's Books</option>
          <option value="mystery and thriller">Mystery & Thriller</option>
          <option value="romance">Romance</option>
          <option value="academic and educational">Academic & Educational</option>
          <option value="business and finance">Business & Finance</option>
          <option value="true crime">True Crime</option>
          <option value="drama and plays">Drama & Plays</option>
          <option value="religious and spiritual">Religious & Spiritual</option>
        </select>
        <select id="search-status">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>
      <div class="input-row">
        <button id="search-button">Search</button>
        <button id="reset-button">Reset Filters</button>
      </div>
    </section>

    <section id="manuscripts-wrapper">
      <div class="manuscripts-header">
        <h3>Submitted Manuscripts</h3>
        <div class="sort-controls">
          <span>Sort by:</span>
          <select id="sort-by">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="title">Title (A-Z)</option>
            <option value="author">Author (A-Z)</option>
          </select>
        </div>
      </div>
      
      <div id="manuscripts">
       <?php include('../backend/api/manuscript/display-editor.php'); ?>
      </div>

      <div class="pagination">
        <button class="page-button" disabled>Previous</button>
        <span class="page-numbers">1 of 3</span>
        <button class="page-button">Next</button>
      </div>
    </section>
  </main>
</body>
</html>