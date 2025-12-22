<?php
session_start();
require_once ('../../lib/Database.php');

if (!isset($_SESSION['user'])) {
    echo '<script>alert("You are NOT logged in");
          window.location.href = "../../../frontend/index.php";</script>';
    exit();
}

session_unset();
session_destroy();
header("Location: ../../../frontend/index.php");
exit();
