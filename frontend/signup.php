<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yolobard - Sign Up</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/auth.css">
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
  
  <main class="auth-container">
    <section class="auth-card">
      <div class="auth-header">
        <h2>Create Your Account</h2>
        <p>Join our community</p>
      </div>
      
      <form class="auth-form" action="../backend/api/auth/signup.php" method="POST">
        <!-- Name Fields -->
        <div class="name-fields">
          <div class="form-group half-width">
            <input type="text" id="first-name" name="first-name" placeholder=" " required>
            <label for="first-name">First Name</label>
          </div>
          <div class="form-group half-width">
            <input type="text" id="last-name" name="last-name" placeholder=" " required>
            <label for="last-name">Last Name</label>
          </div>
        </div>
        
        <!-- Account Type -->
        <div class="form-group">
            <select id="user-role" class="role-select" name="user-role" required>
              <option value="" disabled selected></option>
              <option value="author">Author</option>
              <option value="customer">Customer</option>
            </select>
            <label for="user-role" class="select-label">Account Type</label>
        </div>
        
        <!-- Email -->
        <div class="form-group">
          <input type="email" id="email" name="email" placeholder=" " required>
          <label for="email">Email Address</label>
        </div>
        
        <!-- Password -->
        <div class="form-group">
          <input type="password" id="password" name="password" placeholder=" " required>
          <label for="password">Password</label>
        </div>
        
        <!-- Terms Checkbox -->
        <div class="form-options">
          <label class="terms-checkbox">
            <input type="checkbox" required>
            <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
          </label>
        </div>
        
        <button type="submit" class="button success auth-submit">Create Account</button>
        
        <div class="auth-footer">
          <p>Already have an account? <a href="./login.html">Log in</a></p>
        </div>
      </form>
    </section>
  </main>
</body>
</html>