<?php
$servername = "localhost";
$username   = "root";   // default in XAMPP
$password   = "";       // default in XAMPP
$dbname     = "travelbuddy";

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    // just for debugging, remove later
    // echo "✅ Connected successfully!";
}
?>
