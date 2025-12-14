<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php'); // Include database connection

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $withdraw_amount = $_POST['withdraw_amount'];

    if ($withdraw_amount <= 0) {
        echo "Please enter a valid amount.";
    } else {
        // Check if the user has enough balance
        $sql_balance = "SELECT balance FROM users WHERE id = $user_id";
        $result = $conn->query($sql_balance);
        $user = $result->fetch_assoc();
        $current_balance = $user['balance'];

        if ($withdraw_amount > $current_balance) {
            echo "Insufficient balance.";
        } else {
            // Update user's balance
            $new_balance = $current_balance - $withdraw_amount;
            $sql_update_balance = "UPDATE users SET balance = $new_balance WHERE id = $user_id";
            if ($conn->query($sql_update_balance) === TRUE) {
                // Insert transaction
                $sql_transaction = "INSERT INTO transactions (user_id, type, amount, balance_after_transaction) 
                                    VALUES (?, 'withdrawal', ?, ?)";
                $stmt = $conn->prepare($sql_transaction);
                $stmt->bind_param("idi", $user_id, $withdraw_amount, $new_balance);
                if ($stmt->execute()) {
                    echo "Withdrawal successful! Your new balance is: ₹" . number_format($new_balance, 2);
                }
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}

// Function to fetch the current balance of the user
function getCurrentBalance($user_id) {
    global $conn;
    $sql = "SELECT balance FROM users WHERE id = $user_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return $user['balance'];
    }
    return 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw</title>
    <link rel="stylesheet" href="st.css"> <!-- Link to the styles.css file -->
</head>
<body>
    <header>
        <div class="container">
            <h1>Withdraw from Your Account</h1>
            <p>Withdraw money securely from your account.</p>
        </div>
    </header>

    <main>
        <div class="content-container">
            <h2>Withdrawal Form</h2>
            <form action="withdraw.php" method="POST">
                <label for="withdraw_amount">Amount to Withdraw (₹):</label><br>
                <input type="number" name="withdraw_amount" id="withdraw_amount" required><br><br>
                <input type="submit" value="Withdraw">
            </form>

            <p>Your current balance: ₹<?php echo getCurrentBalance($user_id); ?></p>
            <a href="dashboard.php">Go to Dashboard</a> | <a href="logout.php">Logout</a>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Bank Management System. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
