<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php'); // Include database connection

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deposit_amount = $_POST['deposit_amount'];

    if ($deposit_amount <= 0) {
        echo "Please enter a valid amount.";
    } else {
        // Update user's balance
        $sql_balance = "UPDATE users SET balance = balance + $deposit_amount WHERE id = $user_id";
        if ($conn->query($sql_balance) === TRUE) {
            // Insert transaction
            $balance_after_transaction = getCurrentBalance($user_id);
            $sql_transaction = "INSERT INTO transactions (user_id, type, amount, balance_after_transaction) 
                                VALUES (?, 'deposit', ?, ?)";
            $stmt = $conn->prepare($sql_transaction);
            $stmt->bind_param("idi", $user_id, $deposit_amount, $balance_after_transaction);
            if ($stmt->execute()) {
                echo "Deposit successful! Your new balance is: ₹" . number_format($balance_after_transaction, 2);
            }
        } else {
            echo "Error: " . $conn->error;
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
    <title>Deposit</title>
    <link rel="stylesheet" href="deposit.css"> <!-- Link to the styles.css file -->
</head>
<body>
    <header>
        <div class="container">
            <h1>Deposit to Your Account</h1>
            <p>Deposit money into your account securely.</p>
        </div>
    </header>

    <main>
        <div class="content-container">
            <h2>Deposit Form</h2>
            <form action="deposit.php" method="POST">
                <label for="deposit_amount">Amount to Deposit (₹):</label><br>
                <input type="number" name="deposit_amount" id="deposit_amount" required><br><br>
                <input type="submit" value="Deposit">
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
