<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yolobard - Login</title>
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
          <h2>Welcome Back</h2>
          <p>Log in to your account</p>
        </div>
        <form class="auth-form" action="../backend/api/auth/login.php" method="POST">
          <div class="form-group">
            <select id="user-role" name="user-role" class="role-select" required>
              <option value="" disabled selected></option>
              <option value="author">Author</option>
              <option value="editor">Editor</option>
              <option value="admin">Admin</option>
              <option value="customer">Customer</option>
            </select>
            <label for="user-role" class="select-label">Account Type</label>
          </div>
          <div class="form-group">
            <input type="email" id="email" name="email" placeholder=" " required>
            <label for="email">Email Address</label>
          </div>
          <div class="form-group">
            <input type="password" id="password" name="password" placeholder=" " required>
            <label for="password">Password</label>
          </div>
          <div class="form-options">
            <label>
              <input type="checkbox">
              Remember me
            </label>
            <a href="#" class="forgot-password">Forgot password?</a>
          </div>
          <button type="submit" class="button success auth-submit">Log in</button>
          <div class="auth-footer">
            <p>Don't have an account? <a href="./signup.html">Sign up</a></p>
          </div>
        </form>
      </section>
    </main>
  </body>
    <script>
        document.getElementById('user-role').addEventListener('change', function() {
          document.querySelector('.auth-card').setAttribute('data-role', this.value);
        });
      </script>
</html>