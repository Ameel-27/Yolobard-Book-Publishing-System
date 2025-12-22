<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Yolo-Bard</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/about.css">
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

  <main class="about-container">
    <section class="hero-section">
      <div class="hero-content">
        <h1>Where Publishing Meets Simplicity</h1>
        <p class="tagline">Modernizing the traditional book publishing experience</p>
      </div>
    </section>

    <section class="mission-section">
      <div class="mission-card">
        <h2>Why We Built Yolo-Bard</h2>
        <p>The conventional process of submitting manuscripts, getting them reviewed, and finally published is often slow, manual, and overwhelming for many aspiring authors. We built Yolo-Bard to solve that — by automating the entire publishing pipeline in a clean, user-friendly environment.</p>
      </div>
    </section>

    <section class="process-section">
      <h2>How It Works</h2>
      <div class="process-steps">
        <div class="process-card">
          <div class="process-icon">1</div>
          <h3>Authors Submit</h3>
          <p>Authors submit their manuscripts through our online platform</p>
        </div>
        <div class="process-card">
          <div class="process-icon">2</div>
          <h3>Editor Review</h3>
          <p>Editors review and approve or provide feedback</p>
        </div>
        <div class="process-card">
          <div class="process-icon">3</div>
          <h3>Publish & Sell</h3>
          <p>Approved manuscripts become available in our bookstore</p>
        </div>
      </div>
    </section>

    <section class="team-section">
      <h2>Meet the Team</h2>
      <div class="team-grid">
        <div class="team-card">
          <div class="team-photo placeholder-1"></div>
          <h3>Aleem Ullah Khan</h3>
          <p class="role">Lead Developer</p>
          <p>Coding, development, and ideation</p>
        </div>
        <div class="team-card">
          <div class="team-photo placeholder-2"></div>
          <h3>Shahmeer Qureshi</h3>
          <p class="role">Technical Writer</p>
          <p>Reports and technical documentation</p>
        </div>
        <div class="team-card">
          <div class="team-photo placeholder-3"></div>
          <h3>Muhammad Ali</h3>
          <p class="role">Content Strategist</p>
          <p>Conceptual ideas and editorial insights</p>
        </div>
        <div class="team-card">
          <div class="team-photo placeholder-4"></div>
          <h3>Ahab Khan</h3>
          <p class="role">UX Designer</p>
          <p>Design, user flow, and interface experience</p>
        </div>
      </div>
    </section>

    <section class="cta-section">
      <div class="cta-card">
        <h2>Our Mission</h2>
        <blockquote>To make book publishing so effortless, all you need is your words.</blockquote>
        <p>Whether you're an aspiring writer, a sharp-eyed editor, or a book lover looking for fresh reads — Yolo-Bard welcomes you.</p>
        <div class="contact-info">
          <p>Have questions? Reach us at:</p>
          <a href="mailto:yolobard@gmail.com" class="email-button">yolobard@gmail.com</a>
        </div>
      </div>
    </section>
  </main>
</body>
</html>