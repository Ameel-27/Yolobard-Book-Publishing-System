<?php
session_start();
require_once '../../lib/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['user-role'] ?? '';

    if (empty($email) || empty($password)) {
        die('Email and password are required.');
    }

    $db = Database::getInstance()->getConnection();
    
    // Query to fetch user by email
    $stmt = $db->prepare("SELECT UserID, FirstName, Email, Password, Role FROM Users WHERE Email = ?");
    if (!$stmt) die("Prepare failed: " . $db->error);

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || $password != $user['Password'] || $role != $user['Role']) {
        echo '<script>alert("Invalid email or password.");
              window.location.href = "../../../frontend/login.php";</script>';
        exit();
    }

    // UPDATE LastActive field
    $stmt = $db->prepare("UPDATE Users SET LastActive = NOW() WHERE UserID = ?");
    $stmt->bind_param("i", $user['UserID']);
    $stmt->execute();
    $stmt->close();

    // Retrieve or assign cart
    $stmt = $db->prepare("SELECT CartID FROM Carts WHERE UserID = ?");
    $stmt->bind_param("i", $user['UserID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartRow = $result->fetch_assoc();
    $stmt->close();

    if (!$cartRow) {
        // Call stored procedure to assign cart
        $stmt = $db->prepare("CALL sp_AssignCartToUser(?)");
        $stmt->bind_param("i", $user['UserID']);
        $stmt->execute();
        $result = $stmt->get_result();
        $cartRow = $result->fetch_assoc();
        $stmt->close();
    }

    $cartID = $cartRow['CartID'];

    // Set session and redirect
    $_SESSION['user'] = [
        'UserID'   => $user['UserID'],
        'Username' => $user['FirstName'],
        'Email'    => $user['Email'],
        'Role'     => $user['Role'],
        'CartID'   => $cartID
    ];

    header("Location: ../../../frontend/index.php");
    exit();
}
?>
