<?php
session_start();
require 'db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['expense_id'], $_POST['trip_id'])) {
    $expense_id = intval($_POST['expense_id']);
    $trip_id = intval($_POST['trip_id']);
    $user_id = $_SESSION['user_id'];

    // ✅ Delete expense only if it belongs to a trip of this user
    $sql = "DELETE e FROM expenses e 
            JOIN trips t ON e.trip_id = t.id 
            WHERE e.id = ? AND e.trip_id = ? AND t.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $expense_id, $trip_id, $user_id);

    if ($stmt->execute()) {
        header("Location: expenses.php?trip_id=" . $trip_id . "&msg=Expense Deleted");
        exit();
    } else {
        echo "❌ Error deleting expense.";
    }
} else {
    header("Location: home.php");
    exit();
}
