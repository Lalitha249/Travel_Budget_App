<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get trip id from query string
if (!isset($_GET['trip_id'])) {
    echo "No trip selected!";
    exit();
}

$trip_id = $_GET['trip_id'];

// Handle form submit (update trip)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination = $_POST['destination'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $budget = $_POST['budget'];
    $members = $_POST['members'];

    $update = $conn->prepare("UPDATE trips 
                              SET destination=?, start_date=?, end_date=?, budget=?, members=? 
                              WHERE id=? AND user_id=?");
    $update->bind_param("sssdiis", $destination, $start_date, $end_date, $budget, $members, $trip_id, $user_id);

    if ($update->execute()) {
        header("Location: home.php");
        exit();
    } else {
        echo "Error updating trip: " . $conn->error;
    }
}

// Fetch existing trip details
$sql = "SELECT * FROM trips WHERE id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $trip_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Trip not found or you don’t have permission.";
    exit();
}

$trip = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Trip - TravelBuddy+</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-purple-500 to-pink-500 min-h-screen flex items-center justify-center">

  <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-lg">
    <h2 class="text-2xl font-bold mb-6 text-center text-purple-600">✏️ Edit Trip</h2>
    
    <form method="POST">
      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Destination</label>
        <input type="text" name="destination" value="<?php echo htmlspecialchars($trip['destination']); ?>" 
               class="w-full border rounded-lg px-4 py-2" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Start Date</label>
        <input type="date" name="start_date" value="<?php echo $trip['start_date']; ?>" 
               class="w-full border rounded-lg px-4 py-2" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">End Date</label>
        <input type="date" name="end_date" value="<?php echo $trip['end_date']; ?>" 
               class="w-full border rounded-lg px-4 py-2" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Budget</label>
        <input type="number" name="budget" value="<?php echo $trip['budget']; ?>" 
               class="w-full border rounded-lg px-4 py-2" required>
      </div>

      <div class="mb-6">
        <label class="block text-gray-700 font-semibold mb-2">Members</label>
        <input type="number" name="members" value="<?php echo $trip['members']; ?>" 
               class="w-full border rounded-lg px-4 py-2" required>
      </div>

      <div class="flex justify-between">
        <a href="home.php" class="bg-gray-300 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</a>
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
          Update Trip
        </button>
      </div>
    </form>
  </div>

</body>
</html>
