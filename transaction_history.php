<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php'); // Include database connection

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$username = $user['username'];

// Fetch deposits
$sql_deposits = "SELECT * FROM transactions WHERE user_id = ? AND type = 'deposit' ORDER BY transaction_date DESC";
$stmt_deposits = $conn->prepare($sql_deposits);
$stmt_deposits->bind_param("i", $user_id);
$stmt_deposits->execute();
$deposits = $stmt_deposits->get_result();

// Fetch withdrawals
$sql_withdrawals = "SELECT * FROM transactions WHERE user_id = ? AND type = 'withdrawal' ORDER BY transaction_date DESC";
$stmt_withdrawals = $conn->prepare($sql_withdrawals);
$stmt_withdrawals->bind_param("i", $user_id);
$stmt_withdrawals->execute();
$withdrawals = $stmt_withdrawals->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement</title>
    <link rel="stylesheet" href="statement.css"> <!-- Link to external CSS file -->
</head>
<body>
    <div class="container">
        <h2>Transaction Statement</h2>
        <h3>Welcome, <?php echo htmlspecialchars($username); ?></h3>

        <h4>Deposits</h4>
        <table>
            <thead>
                <tr>
                    <th>Amount (₹)</th>
                    <th>Date</th>
                    <th>Balance After Deposit (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($deposits->num_rows > 0) {
                    while ($row = $deposits->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . number_format($row['amount'], 2) . "</td>";
                        echo "<td>" . $row['transaction_date'] . "</td>";
                        echo "<td>" . number_format($row['balance_after_transaction'], 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No deposits found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h4>Withdrawals</h4>
        <table>
            <thead>
                <tr>
                    <th>Amount (₹)</th>
                    <th>Date</th>
                    <th>Balance After Withdrawal (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($withdrawals->num_rows > 0) {
                    while ($row = $withdrawals->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . number_format($row['amount'], 2) . "</td>";
                        echo "<td>" . $row['transaction_date'] . "</td>";
                        echo "<td>" . number_format($row['balance_after_transaction'], 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No withdrawals found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
