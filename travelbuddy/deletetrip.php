<?php
session_start();
require 'db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// ✅ Ensure trip_id is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['trip_id'])) {
    $trip_id = intval($_POST['trip_id']);
    $user_id = $_SESSION['user_id'];

    // Delete only if trip belongs to logged-in user
    $stmt = $conn->prepare("DELETE FROM trips WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $trip_id, $user_id);

    if ($stmt->execute()) {
        header("Location: home.php?msg=Trip Deleted Successfully");
        exit();
    } else {
        echo "❌ Error deleting trip.";
    }
} else {
    header("Location: home.php");
    exit();
}
