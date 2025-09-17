<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

// ✅ Get trip ID from URL
if (!isset($_GET['trip_id'])) {
  die("Trip not specified!");
}
$trip_id = $_GET['trip_id'];

// ✅ Fetch trip details from DB
$stmt = $conn->prepare("SELECT * FROM trips WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $trip_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$trip = $result->fetch_assoc();

if (!$trip) {
  die("Trip not found or you don’t have access!");
}

// ✅ Calculations
$start = new DateTime($trip['start_date']);
$end = new DateTime($trip['end_date']);
$days = $start->diff($end)->days + 1;

$per_person = $trip['budget'] / $trip['members'];

// Months until trip start
$today = new DateTime();
$months_until_trip = max(1, $today->diff($start)->m + ($today->diff($start)->y * 12));
$monthly_saving = ceil($trip['budget'] / $months_until_trip);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trip Details - TravelBuddy+</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-purple-500 to-pink-500 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-purple-600">TravelBuddy+</h1>
    <a href="home.php" class="text-purple-600 hover:underline">← Back to Dashboard</a>
  </header>

  <!-- Trip Details -->
  <main class="flex-grow p-6">
    <div class="bg-white p-6 rounded-2xl shadow-xl max-w-4xl mx-auto">
      <h2 class="text-3xl font-bold text-purple-600 mb-4">Trip to <?php echo htmlspecialchars($trip['destination']); ?> 🌍</h2>

      <div class="grid grid-cols-2 gap-6">
        <div class="bg-purple-50 p-4 rounded-xl">
          <p class="text-gray-600">Destination</p>
          <p class="font-semibold text-lg"><?php echo htmlspecialchars($trip['destination']); ?></p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl">
          <p class="text-gray-600">Trip Duration</p>
          <p class="font-semibold text-lg"><?php echo $days; ?> Days (<?php echo $trip['start_date']; ?> - <?php echo $trip['end_date']; ?>)</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl">
          <p class="text-gray-600">Members</p>
          <p class="font-semibold text-lg"><?php echo $trip['members']; ?></p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl">
          <p class="text-gray-600">Total Budget</p>
          <p class="font-semibold text-lg">₹ <?php echo number_format($trip['budget']); ?></p>
        </div>
      </div>

      <!-- Insights Section -->
      <div class="mt-8 grid grid-cols-2 gap-6">
        <div class="bg-green-50 p-4 rounded-xl">
          <p class="text-gray-600">Per Person Share</p>
          <p class="font-semibold text-lg">₹ <?php echo number_format($per_person); ?></p>
        </div>
        <div class="bg-blue-50 p-4 rounded-xl">
          <p class="text-gray-600">Savings Needed Per Month</p>
          <p class="font-semibold text-lg">₹ <?php echo number_format($monthly_saving); ?></p>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-8 flex gap-4">
        <a href="expenses.php?trip_id=<?php echo $trip_id; ?>" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">Add Expense</a>
        <a href="edittrip.php?trip_id=<?php echo $trip_id; ?>" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">Edit Trip</a>
        <a href="deletetrip.php?trip_id=<?php echo $trip_id; ?>" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition">Delete Trip</a>
      </div>
    </div>
  </main>

</body>
</html>
