<?php
session_start();
include 'db.php';

// Check login
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

// Fetch trip
$sql = "SELECT * FROM trips WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $trip_id, $user_id);
$stmt->execute();
$trip = $stmt->get_result()->fetch_assoc();

if (!$trip) {
  echo "Trip not found!";
  exit();
}

// Handle new expense
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
  $title = $_POST['title'];
  $amount = $_POST['amount'];
  $category = $_POST['category'];

  $insert_sql = "INSERT INTO expenses (trip_id, title, amount, category) VALUES (?, ?, ?, ?)";
  $insert_stmt = $conn->prepare($insert_sql);
  $insert_stmt->bind_param("isis", $trip_id, $title, $amount, $category);
  $insert_stmt->execute();
}

// Fetch all expenses
$expenses = [];
$expenses_sql = "SELECT * FROM expenses WHERE trip_id = ?";
$expenses_stmt = $conn->prepare($expenses_sql);
$expenses_stmt->bind_param("i", $trip_id);
$expenses_stmt->execute();
$expenses_result = $expenses_stmt->get_result();

$total_spent = 0;
while ($row = $expenses_result->fetch_assoc()) {
  $total_spent += $row['amount'];
  $expenses[] = $row;
}

$remaining_budget = $trip['budget'] - $total_spent;
$progress = ($trip['budget'] > 0) ? min(100, ($total_spent / $trip['budget']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Expenses - <?php echo htmlspecialchars($trip['destination']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-purple-500 to-pink-500 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-3xl backdrop-blur-md bg-white/20 border border-white/30 rounded-2xl p-8 shadow-lg text-white">
    <h2 class="text-3xl font-bold mb-6 text-center">💸 Expenses for <?php echo htmlspecialchars($trip['destination']); ?></h2>

    <!-- Budget Tracker -->
    <div class="mb-6">
      <p class="mb-2">Budget: <b>₹<?php echo number_format($trip['budget']); ?></b></p>
      <p class="mb-2">Spent: <b>₹<?php echo number_format($total_spent); ?></b></p>
      <p class="mb-2">Remaining: <b>₹<?php echo number_format($remaining_budget); ?></b></p>
      <div class="w-full bg-white/30 rounded-full h-4 overflow-hidden">
        <div class="bg-green-500 h-4" style="width: <?php echo $progress; ?>%"></div>
      </div>
    </div>

    <!-- Add Expense Form -->
    <form method="POST" class="mb-6 space-y-4">
      <div>
        <label class="block mb-1">Expense Title</label>
        <input type="text" name="title" required
               class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/30 text-black focus:ring-2 focus:ring-purple-400">
      </div>
      <div>
        <label class="block mb-1">Amount (₹)</label>
        <input type="number" name="amount" required
               class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/30 text-black focus:ring-2 focus:ring-purple-400">
      </div>
      <div>
        <label class="block mb-1">Category</label>
        <select name="category" required
                class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/30 text-black focus:ring-2 focus:ring-purple-400">
          <option value="Food">🍔 Food</option>
          <option value="Travel">✈️ Travel</option>
          <option value="Stay">🏨 Stay</option>
          <option value="Shopping">🛍️ Shopping</option>
          <option value="Misc">🔖 Misc</option>
        </select>
      </div>
      <button type="submit" class="w-full bg-purple-600 py-2 rounded-lg hover:bg-purple-700 transition text-white">
        ➕ Add Expense
      </button>
    </form>

    <!-- Expense List -->
    <h3 class="text-2xl font-semibold mb-3">📋 Expense List</h3>
    <?php if (!empty($expenses)) { ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border border-white/30 rounded-lg overflow-hidden">
          <thead class="bg-white/30 text-black">
            <tr>
              <th class="px-4 py-2">Title</th>
              <th class="px-4 py-2">Amount (₹)</th>
              <th class="px-4 py-2">Category</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($expenses as $exp) { ?>
              <tr class="border-t border-white/30">
                <td class="px-4 py-2"><?php echo htmlspecialchars($exp['title']); ?></td>
                <td class="px-4 py-2">₹<?php echo number_format($exp['amount']); ?></td>
                <td class="px-4 py-2"><?php echo htmlspecialchars($exp['category']); ?></td>
                <td class="px-4 py-2">
                  <form action="deleteexpense.php" method="POST" style="display:inline;">
                    <input type="hidden" name="expense_id" value="<?php echo $exp['id']; ?>">
                    <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                    <button type="submit"
                            onclick="return confirm('Delete this expense?');"
                            class="bg-red-500 px-2 py-1 rounded hover:bg-red-600 text-white text-sm">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } else { ?>
      <p class="text-gray-200">No expenses yet. Start adding!</p>
    <?php } ?>

    <!-- Back button -->
    <div class="text-center mt-6">
      <a href="home.php" class="text-white underline">⬅ Back to Home</a>
    </div>
  </div>

</body>
</html>
