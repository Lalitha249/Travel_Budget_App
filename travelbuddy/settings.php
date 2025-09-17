<?php
session_start();
require 'db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">⚙️ Settings</h2>

    <ul class="space-y-4">
      <li>
        <a href="profile.php" class="block p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
          👤 Profile Settings
        </a>
      </li>
      <li>
        <a href="notifications.php" class="block p-4 bg-green-500 text-white rounded-lg hover:bg-green-600">
          🔔 Notifications
        </a>
      </li>
      <li>
        <a href="privacy.php" class="block p-4 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
          🔒 Privacy & Security
        </a>
      </li>
      <li>
        <a href="logout.php" class="block p-4 bg-red-500 text-white rounded-lg hover:bg-red-600">
          🚪 Logout
        </a>
      </li>
    </ul>

    <!-- Back button -->
    <div class="text-center mt-6">
      <a href="home.php" class="text-blue-500 underline">⬅ Back to Home</a>
    </div>
  </div>

</body>
</html>
