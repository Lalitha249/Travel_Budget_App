<?php
session_start();
require 'db.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ✅ Collect and sanitize inputs
    $destination = trim($_POST['destination']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $budget = $_POST['budget'];
    $members = $_POST['members'];
    $user_id = $_SESSION['user_id'];

    // ✅ Basic validation
    if (empty($destination) || empty($start_date) || empty($end_date) || empty($budget) || empty($members)) {
        echo "⚠️ Please fill in all fields.";
        exit();
    }

    if ($start_date > $end_date) {
        echo "⚠️ End date cannot be before start date!";
        exit();
    }

    if ($budget <= 0) {
        echo "⚠️ Budget must be greater than 0.";
        exit();
    }

    if ($members < 1) {
        echo "⚠️ Number of travelers must be at least 1.";
        exit();
    }

    // ✅ Insert into database
    $stmt = $conn->prepare("INSERT INTO trips (user_id, destination, start_date, end_date, budget, members) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdi", $user_id, $destination, $start_date, $end_date, $budget, $members);

    if ($stmt->execute()) {
        // Redirect to home with success message
        $_SESSION['success'] = "🎉 Your adventure to $destination has been planned!";
        header("Location: home.php");
        exit();
    } else {
        echo "❌ Error saving trip: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
