<?php
require_once '../../lib/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fName = $_POST['first-name'] ?? '';
    $lName = $_POST['last-name'] ?? '';
    $role = $_POST['user-role'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($fName) || empty($lName) || empty($role) || empty($email) || empty($password)) {
        die('All fields are required');
    }

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare('SELECT UserID FROM Users WHERE Email = ?');
    if (!$stmt) die("Prepare failed: " . $db->error);

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        die("Email already exists");
    }
    $stmt->close();

    $stmt = $db->prepare('INSERT INTO Users (Email, Password, FirstName, LastName, Role, CreatedAt)
                          VALUES (?, ?, ?, ?, ?, NOW())');
    if (!$stmt) die("Prepare failed: " . $db->error);

    $stmt->bind_param('sssss', $email, $password, $fName, $lName, $role);
    if (!$stmt->execute()) {
        die("Insert failed: " . $stmt->error);
    }

    $stmt->close();

    echo "<script>alert('Registration Successful! Login to your account now!'); window.location.href='../../../frontend/login.php';</script>";
    exit();
}
