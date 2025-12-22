<?php
require_once '../backend/lib/UserFactory.php';  // <- Matches URL root

$role = $_SESSION['user']['Role'] ?? null;
$userId = $_SESSION['user']['UserID'] ?? null;

$user = null;
$navItems = [];

if ($role && $userId) {
    try {
        $user = UserFactory::create($role, $userId);
        $navItems = $user->getNavItems();
    } catch (Exception $e) {
        $navItems = [
            'Home' => '/yolobard/frontend/index.php',
            'Book Store' => '/yolobard/frontend/bookstore.php',
            'About Us' => '/yolobard/frontend/about.php',
            'Login' => '/yolobard/frontend/login.php',
            'Signup' => '/yolobard/frontend/signup.php',
        ];
        die($e->getMessage());
    }
} else {
    $navItems = [
            'Home' => '/yolobard/frontend/index.php',
            'Book Store' => '/yolobard/frontend/bookstore.php',
            'About Us' => '/yolobard/frontend/about.php',
            'Login' => '/yolobard/frontend/login.php',
            'Signup' => '/yolobard/frontend/signup.php',
    ];
}
?>

<div id="navbar" class="nav-items">
  <?php foreach ($navItems as $label => $link): ?>
    <a href="<?php echo $link ?>" class="nav-item">
      <?php echo $label ?>
    </a>
  <?php endforeach; ?>
</div>

<script>
  const currentPath = window.location.pathname.split("/").pop();
  document.querySelectorAll("#navbar a").forEach(link => {
    const linkPath = link.getAttribute("href").split("/").pop();
    if (linkPath === currentPath) {
      link.classList.add("active");
    }
  });
</script>
