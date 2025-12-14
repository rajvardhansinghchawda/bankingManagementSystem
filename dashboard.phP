<?php
session_start();

// Check if the user is logged in, else redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php');

// Fetch the logged-in user's details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
    $balance = $user['balance'];
} else {
    $username = 'Unknown';
    $balance = 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Dashboard</title>
    <link rel="stylesheet" href="dashbord.css">
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h2>FarziBank</h2>
        </div>
        <ul class="nav-links">
            <li><a href="deposit.php">Deposit Amount</a></li>
            <li><a href="withdraw.php">Withdraw Amount</a></li>
            <li><a href="transaction_history.php">Transaction History</a></li> 
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

   <!-- Main Content -->
<div class="main-content">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
    <p>Your current balance is: ₹<?php echo number_format($balance, 2); ?></p>

    <!-- Recent Transactions -->
    <h3>Recent Transactions</h3>
    <table >
        
        <thead>
            <tr>
                <th>Transaction Type</th>
                <th>Amount (₹)</th>
                <th>Date</th>
                <th>Balance After Transaction (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch recent transactions for the user
            $sql_transactions = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT 5";  // Limit to 5 recent transactions
            $stmt_transactions = $conn->prepare($sql_transactions);
            $stmt_transactions->bind_param("i", $user_id);
            $stmt_transactions->execute();
            $result_transactions = $stmt_transactions->get_result();

            // Check if there are any transactions
            if ($result_transactions->num_rows > 0) {
                while ($row = $result_transactions->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . number_format($row['amount'], 2) . "</td>";
                    echo "<td>" . date('d M Y, H:i', strtotime($row['transaction_date'])) . "</td>";  // Format date
                    echo "<td>" . number_format($row['balance_after_transaction'], 2) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No transactions found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="transaction_history.php">View All Transactions</a> <!-- Link to full transaction history -->
</div>


</body>
</html>
