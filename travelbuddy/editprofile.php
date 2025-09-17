<?php
session_start();
require 'db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch current user details
$sql = "SELECT id, name, email, profile_pic, password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// ✅ Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $profile_pic = $user['profile_pic']; // default

    // ✅ Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        }
    }

    // ✅ Handle password update (optional)
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $update_sql = "UPDATE users SET name=?, email=?, profile_pic=?, password=? WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $name, $email, $profile_pic, $new_password, $user_id);
    } else {
        $update_sql = "UPDATE users SET name=?, email=?, profile_pic=? WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $profile_pic, $user_id);
    }

    if ($update_stmt->execute()) {
        header("Location: profile.php?msg=Profile Updated");
        exit();
    } else {
        echo "❌ Error updating profile!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">✏️ Edit Profile</h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <!-- Name -->
      <div>
        <label class="block font-semibold">Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" 
               class="w-full px-4 py-2 border rounded-lg" required>
      </div>

      <!-- Email -->
      <div>
        <label class="block font-semibold">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
               class="w-full px-4 py-2 border rounded-lg" required>
      </div>

      <!-- Profile Pic -->
      <div>
        <label class="block font-semibold">Profile Picture</label>
        <?php if (!empty($user['profile_pic'])) { ?>
          <img src="<?php echo $user['profile_pic']; ?>" class="w-20 h-20 rounded-full mb-2">
        <?php } ?>
        <input type="file" name="profile_pic" class="w-full px-2 py-2 border rounded-lg">
      </div>

      <!-- Password -->
      <div>
        <label class="block font-semibold">New Password (optional)</label>
        <input type="password" name="new_password" placeholder="Enter new password" 
               class="w-full px-4 py-2 border rounded-lg">
      </div>

      <!-- Buttons -->
      <div class="flex justify-between">
        <a href="profile.php" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">⬅ Back</a>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">💾 Save</button>
      </div>
    </form>
  </div>

</body>
</html>
