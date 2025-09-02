<?php
session_start();
require 'db.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch trips for this user
$stmt = $conn->prepare("SELECT * FROM trips WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$trips = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - TravelBuddy+</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-purple-500 to-pink-500 min-h-screen flex flex-col">
  
  <!-- Navbar -->
  <nav class="bg-white shadow-md p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-purple-600">🌍 TravelBuddy+</h1>
    <div>
      <a href="tripsetup.html" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">➕ Add Trip</a>
      <a href="logout.php" class="ml-4 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Logout</a>
    </div>
  </nav>

  <!-- Main Dashboard -->
  <div class="flex-grow p-8">
    <h2 class="text-3xl font-bold text-white mb-6">Your Trips</h2>

    <?php if (isset($_GET['msg'])): ?>
      <p class="mb-4 text-green-200 font-semibold"><?php echo htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (count($trips) > 0): ?>
        <?php foreach ($trips as $trip): ?>
          <div class="bg-white p-6 rounded-2xl shadow-xl">
            <h3 class="text-xl font-bold text-purple-600"><?php echo htmlspecialchars($trip['destination']); ?></h3>
            <p class="text-gray-600">📅 <?php echo $trip['start_date']; ?> → <?php echo $trip['end_date']; ?></p>
            <p class="text-gray-600">💰 Budget: ₹<?php echo number_format($trip['budget']); ?></p>
            <p class="text-gray-600">👥 Members: <?php echo $trip['members']; ?></p>

            <div class="mt-4 space-y-2">
              <!-- View Button -->
              <a href="tripdetails.php?id=<?php echo $trip['id']; ?>" 
                 class="w-full block text-center bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                View Plan
              </a>

              <!-- Delete Button -->
              <form action="deletetrip.php" method="POST" 
                    onsubmit="return confirm('Are you sure you want to delete this trip?');">
                <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
                <button type="submit" 
                        class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                  ❌ Delete Trip
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-white text-lg">No trips yet. Start by adding one! 🎉</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
