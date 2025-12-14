<?php
include('db.php');  // Include the database connection file

// Start session to manage logged-in users
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database for the user
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session and redirect to the dashboard
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");  // Redirect to a dashboard or home page
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <link rel="stylesheet" href="loginmain.css">
    
</head>
<body>
    <header>
        
        <h2>Login to Your Account</h2>
    </header>
    <form action="login.php" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" name="username" id="username" required><br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>
        
        <input type="submit" value="Login">
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </form>

</body>
</html>
