<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "travelbuddy";

// connect to db
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        // email already exists
        echo "<script>alert('Email already registered! Please login.'); window.location.href='login.html';</script>";
    } else {
        // insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Signup successful! Please login.'); window.location.href='login.html';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $checkEmail->close();
}
$conn->close();
?>
