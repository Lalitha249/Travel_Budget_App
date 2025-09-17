<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch trips
$sql = "SELECT * FROM trips WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home - TravelBuddy+</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-r from-purple-500 to-pink-500 min-h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="flex justify-between items-center p-6 text-white font-bold text-xl">
    <div class="flex justify-between items-center mb-6">
  <h2 class="text-2xl font-bold text-white">🏠 Welcome to TravelBuddy+</h2>
  
  <!-- Profile Button -->
  <a href="profile.php" class="flex items-center space-x-2 bg-white/20 px-4 py-2 rounded-full hover:bg-white/30 transition">
    <span class="text-white">👤 Profile</span>
  </a>
  <a href="settings.php" class="px-4 py-2 text-white bg-gray-700 rounded hover:bg-gray-800">
  ⚙️ Settings
</a>

</div>
    <div>
      <a href="logout.php" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100">
        Logout
      </a>
    </div>
  </nav>

  <!-- Trips Section -->
  <div class="flex-1 px-6 py-4">
    <h2 class="text-3xl font-bold text-white mb-6">Your Trips</h2>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="backdrop-blur-md bg-white/20 border border-white/30 rounded-2xl p-6 shadow-lg text-white">
          <h3 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($row['destination']); ?></h3>
          <p class="mb-2"><i class="fas fa-wallet mr-2"></i> Budget: ₹<?php echo $row['budget']; ?></p>
          <p class="mb-2"><i class="fas fa-users mr-2"></i> Members: <?php echo $row['members']; ?></p>
          <p class="mb-4"><i class="fas fa-calendar-alt mr-2"></i> 
            <?php echo $row['start_date']; ?> → <?php echo $row['end_date']; ?>
          </p>
          <div class="flex gap-3">
            <a href="tripdetails.php?trip_id=<?php echo $row['id']; ?>" 
               class="bg-purple-600 px-3 py-2 rounded-lg hover:bg-purple-700 text-sm">View</a>
            <a href="edittrip.php?trip_id=<?php echo $row['id']; ?>" 
               class="bg-blue-500 px-3 py-2 rounded-lg hover:bg-blue-600 text-sm">Edit</a>
            <a href="expenses.php?trip_id=<?php echo $row['id']; ?>" 
               class="bg-green-500 px-3 py-2 rounded-lg hover:bg-green-600 text-sm">Add Expense</a>
            <a href="itinerary.php?trip_id=<?php echo $row['id']; ?>" 
               class="bg-indigo-600 px-3 py-2 rounded-lg hover:bg-indigo-700 text-sm">Itinerary</a>
              </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <!-- Floating Button -->
  <a href="tripsetup.html" 
     class="fixed bottom-6 right-6 bg-white text-purple-600 text-3xl font-bold rounded-full w-14 h-14 flex items-center justify-center shadow-lg hover:bg-gray-100">
    +
  </a>

</body>
</html>
