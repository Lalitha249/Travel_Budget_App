<?php
session_start();
require 'db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$trip_id = $_GET['trip_id'] ?? null;

if (!$trip_id) {
    header("Location: home.php");
    exit();
}

// ✅ Check trip belongs to user
$sql = "SELECT * FROM trips WHERE id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $trip_id, $user_id);
$stmt->execute();
$trip = $stmt->get_result()->fetch_assoc();

if (!$trip) {
    echo "❌ Trip not found!";
    exit();
}

// ✅ Add activity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['day'], $_POST['activity'])) {
    $day = $_POST['day'];
    $activity = $_POST['activity'];
    $notes = $_POST['notes'] ?? null;

    $insert = $conn->prepare("INSERT INTO itinerary (trip_id, day, activity, notes) VALUES (?, ?, ?, ?)");
    $insert->bind_param("isss", $trip_id, $day, $activity, $notes);
    $insert->execute();
}

// ✅ Fetch itinerary
$items = [];
$result = $conn->prepare("SELECT * FROM itinerary WHERE trip_id=? ORDER BY day ASC");
$result->bind_param("i", $trip_id);
$result->execute();
$data = $result->get_result();
while ($row = $data->fetch_assoc()) {
    $items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Itinerary - <?php echo htmlspecialchars($trip['destination']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-500 to-green-500 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-3xl bg-white/20 backdrop-blur-md border border-white/30 rounded-2xl p-8 shadow-lg text-white">
    <h2 class="text-3xl font-bold mb-6 text-center">🗓️ Itinerary for <?php echo htmlspecialchars($trip['destination']); ?></h2>

    <!-- Add Activity -->
    <form method="POST" class="mb-6 space-y-4">
      <div>
        <label class="block mb-1">Day</label>
        <input type="date" name="day" required
               class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/40 text-black focus:ring-2 focus:ring-blue-400">
      </div>
      <div>
        <label class="block mb-1">Activity</label>
        <input type="text" name="activity" required
               class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/40 text-black focus:ring-2 focus:ring-blue-400">
      </div>
      <div>
        <label class="block mb-1">Notes (optional)</label>
        <textarea name="notes"
                  class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/40 text-black focus:ring-2 focus:ring-blue-400"></textarea>
      </div>
      <button type="submit" class="w-full bg-blue-600 py-2 rounded-lg hover:bg-blue-700 transition">
        ➕ Add Activity
      </button>
    </form>

    <!-- Activity List -->
    <h3 class="text-2xl font-semibold mb-3">📋 Planned Activities</h3>
    <?php if (!empty($items)) { ?>
      <ul class="space-y-3">
        <?php foreach ($items as $act) { ?>
          <li class="p-4 bg-white/30 rounded-lg shadow">
            <p><b>📅 <?php echo htmlspecialchars($act['day']); ?></b></p>
            <p>✅ <?php echo htmlspecialchars($act['activity']); ?></p>
            <?php if (!empty($act['notes'])) { ?>
              <p class="text-sm text-gray-200">📝 <?php echo htmlspecialchars($act['notes']); ?></p>
            <?php } ?>
          </li>
        <?php } ?>
      </ul>
    <?php } else { ?>
      <p class="text-gray-200">No activities yet. Start planning!</p>
    <?php } ?>

    <!-- Back button -->
    <div class="text-center mt-6">
      <a href="home.php" class="text-white underline">⬅ Back to Home</a>
    </div>
  </div>

</body>
</html>
