<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yolobard - Book Publishing Site</title>
    <link rel="stylesheet" href="./css/index.css">
    <script src="./js/index.js" defer></script>
  </head>
  <body>
    <nav>
      <div class="nav-brand">
        <a href="./index.php">Yolo-Bard</a>
      </div>
      <div id="nav-button">&equiv;</div>
      <?php include('../backend/api/auth/navbar.php'); ?>
    </nav>
    <section class="service" id="sign-up">
      <div class="service-content">
        <h2 class="service-title">Publish now</h2>
        <p class="service-description">Ready to Publish? Join Our Community of Authors Today and Bring Your Book To Life.</p>
        <a href="./signup.php" class="service-button">Sign-up</a>
      </div>
    </section>
    <section class="service" id="book-store">
      <div class="service-content">
        <h2 class="service-title">Explore Books</h2>
        <p class="service-description">Browse a growing collection of fresh, independent titles across genres.</p>
        <a href="./bookstore.php" class="service-button">Shop now</a>
      </div>
    </section>
    <section class="service" id="about-us">
      <div class="service-content">
        <h2 class="service-title">About Us</h2>
        <p class="service-description">We connect authors, editors, and readers through a streamlined publishing process — from submission to storefront.</p>
        <a href="./about.php" class="service-button">Learn More</a>
      </div>
    </section>
  </body>
  <script>
  </script>
</html>
