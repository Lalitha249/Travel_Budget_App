<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user details
$sql = "SELECT id, name, email, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md text-center">
    <h2 class="text-2xl font-bold mb-6">👤 My Profile</h2>

    <!-- Profile Picture -->
    <?php if (!empty($user['profile_pic'])) { ?>
      <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" 
           alt="Profile Picture" 
           class="w-24 h-24 rounded-full mx-auto mb-4">
    <?php } else { ?>
      <div class="w-24 h-24 rounded-full mx-auto mb-4 bg-gray-300 flex items-center justify-center text-gray-700">
        <span class="text-xl">No Pic</span>
      </div>
    <?php } ?>

    <!-- User Details -->
    <p><b>Name:</b> <?php echo htmlspecialchars($user['name']); ?></p>
    <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>

    <div class="mt-6 flex justify-between">
      <a href="home.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">🏠 Home</a>
      <a href="editprofile.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">✏️ Edit Profile</a>
      <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">🚪 Logout</a>
    </div>
  </div>

</body>
</html>
