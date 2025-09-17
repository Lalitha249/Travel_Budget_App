<?php
session_start();
require __DIR__ . '/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('⚠️ Please fill all fields'); window.history.back();</script>";
        exit;
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('❌ Email already registered. Please log in.'); window.location.href='login.html';</script>";
        exit;
    }
    $check_stmt->close();

    // ✅ Hash password before saving
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Account created successfully! Please log in.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('❌ Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
}
?>
