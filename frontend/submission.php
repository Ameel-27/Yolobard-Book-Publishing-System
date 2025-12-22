<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Manuscript - Yolo-Bard</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/submission.css">
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

  <main class="submission-container">
    <h1>Submit Your Manuscript</h1>
    
    <form class="submission-form" action="../backend/api/manuscript/submit-author.php" method="POST" enctype="multipart/form-data">
      <!-- First Row: Title, Author, Genre -->
      <div class="form-row">
        <div class="form-group">
          <label for="book-title">Book Title</label>
          <input type="text" id="book-title" name="title" required>
        </div>
                
        <div class="form-group">
          <label for="genre">Genre</label>
          <select id="genre" name="genre" required>
            <option value="" disabled selected>Select genre</option>
            <option value="Fiction">Fiction</option>
            <option value="Horror">Horror</option>
            <option value="Children's Books">Children's Books</option>
            <option value="Mystery and Thriller">Mystery & Thriller</option>
            <option value="Romance">Romance</option>
            <option value="Academic and Educational">Academic & Educational</option>
            <option value="Business and Finance">Business & Finance</option>
            <option value="True Crime">True Crime</option>
            <option value="Drama and Plays">Drama & Plays</option>
            <option value="Religious and Spiritual">Religious & Spiritual</option>
          </select>
        </div>
      </div>
      
      <!-- Second Row: Description -->
      <div class="form-row">
        <div class="form-group full-width">
          <label for="description">Book Description</label>
          <textarea id="description" rows="4" name="description" required></textarea>
        </div>
      </div>
      
      <!-- Third Row: Price, File Uploads -->
      <div class="form-row">
        <div class="form-group">
          <label for="price">Price (USD)</label>
          <input type="number" id="price" name="price" min="0" step="0.01" required>
        </div>
        
        <div class="form-group">
          <label for="title-page">Title Page Image</label>
          <input type="file" id="title-page" name="cover" accept="image/*" required>
          <div class="file-hint">JPEG or PNG, max 5MB</div>
        </div>
        
        <div class="form-group">
          <label for="manuscript">Manuscript File</label>
          <input type="file" id="manuscript" name="manuscript" accept=".pdf,.docx" required>
          <div class="file-hint">PDF or DOCX, max 20MB</div>
        </div>
      </div>
      
      <!-- Terms and Submit -->
      <div class="form-row submit-row">
        <div class="terms-group">
          <input type="checkbox" id="terms" required>
          <label for="terms">I confirm this is my original work and agree to the <a href="#">Submission Terms</a></label>
        </div>
        <button type="submit" class="button success submit-btn">Submit Manuscript</button>
      </div>
    </form>
  </main>
</body>
</html>