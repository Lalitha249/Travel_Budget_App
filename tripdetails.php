<?php
session_start();
require 'db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// ✅ Get trip id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid trip ID.");
}
$trip_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// ✅ Fetch trip
$stmt = $conn->prepare("SELECT * FROM trips WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $trip_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$trip = $result->fetch_assoc();

if (!$trip) {
    die("Trip not found.");
}

// --- Trip details ---
$destination = $trip['destination'];
$start_date  = new DateTime($trip['start_date']);
$end_date    = new DateTime($trip['end_date']);
$budget      = (float)$trip['budget'];
$members     = (int)$trip['members'];

// --- Per person split ---
$per_person = $budget / max($members, 1);

// --- Calculate time left ---
$today = new DateTime();
if ($start_date <= $today) {
    $days_left   = 1;
    $weeks_left  = 1;
    $months_left = 1;
} else {
    $interval   = $today->diff($start_date);
    $days_left  = max($interval->days, 1);
    $weeks_left = (int)ceil($days_left / 7);
    $months_left = ($interval->y * 12) + $interval->m + ($interval->d > 0 ? 1 : 0);
    if ($months_left < 1) $months_left = 1;
}

// --- Saving plan ---
$total_daily_saving   = $budget / $days_left;
$total_weekly_saving  = $budget / $weeks_left;
$total_monthly_saving = $budget / $months_left;

$pp_daily_saving   = $per_person / $days_left;
$pp_weekly_saving  = $per_person / $weeks_left;
$pp_monthly_saving = $per_person / $months_left;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trip Details - TravelBuddy+</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-purple-500 to-pink-500 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg">
    <h2 class="text-3xl font-bold text-purple-600 mb-4">🌍 <?php echo htmlspecialchars($destination); ?></h2>

    <p class="text-gray-700"><strong>Start Date:</strong> <?php echo $start_date->format('d M Y'); ?></p>
    <p class="text-gray-700"><strong>End Date:</strong> <?php echo $end_date->format('d M Y'); ?></p>
    <p class="text-gray-700"><strong>Total Budget:</strong> ₹<?php echo number_format($budget, 2); ?></p>
    <p class="text-gray-700"><strong>Members:</strong> <?php echo $members; ?> people</p>
    <p class="text-gray-700 mb-4"><strong>Per Person:</strong> ₹<?php echo number_format($per_person, 2); ?></p>

    <h3 class="text-xl font-semibold text-purple-600 mb-2">💡 Smart Saving Plan</h3>
    <ul class="list-disc pl-5 space-y-2 text-gray-700">
      <li>Days left until trip: <strong><?php echo $days_left; ?> days</strong></li>

      <li>
        Daily target (per person): <strong>₹<?php echo number_format($pp_daily_saving, 2); ?></strong>
        <span class="text-gray-500 text-sm">(total: ₹<?php echo number_format($total_daily_saving, 2); ?>/day)</span>
      </li>
      <li>
        Weekly target (per person): <strong>₹<?php echo number_format($pp_weekly_saving, 2); ?></strong>
        <span class="text-gray-500 text-sm">(total: ₹<?php echo number_format($total_weekly_saving, 2); ?>/week)</span>
      </li>
      <li>
        Monthly target (per person): <strong>₹<?php echo number_format($pp_monthly_saving, 2); ?></strong>
        <span class="text-gray-500 text-sm">(total: ₹<?php echo number_format($total_monthly_saving, 2); ?>/month)</span>
      </li>
    </ul>

    <a href="home.php" 
       class="mt-6 block text-center bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
       ⬅ Back to Dashboard
    </a>
  </div>
</body>
</html>
