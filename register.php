<?php
session_start();

// Include database connection
include('db.php');

// Define variables and initialize with empty values
$first_name = $last_name = $father_name = $contact_number = $username = $password = $confirm_password = "";
$first_name_err = $last_name_err = $father_name_err = $contact_number_err = $username_err = $password_err = $confirm_password_err = "";

// Process the form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate First Name
    if (empty(trim($_POST["first_name"]))) {
        $first_name_err = "Please enter your first name.";
    } else {
        $first_name = trim($_POST["first_name"]);
    }
    
    // Validate Last Name
    if (empty(trim($_POST["last_name"]))) {
        $last_name_err = "Please enter your last name.";
    } else {
        $last_name = trim($_POST["last_name"]);
    }
    
    // Validate Father's Name
    if (empty(trim($_POST["father_name"]))) {
        $father_name_err = "Please enter your father's name.";
    } else {
        $father_name = trim($_POST["father_name"]);
    }
    
    // Validate Contact Number
    if (empty(trim($_POST["contact_number"]))) {
        $contact_number_err = "Please enter your contact number.";
    } else {
        $contact_number = trim($_POST["contact_number"]);
    }
    
    // Validate Username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate Confirm Password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check for errors before inserting into the database
    if (empty($first_name_err) && empty($last_name_err) && empty($father_name_err) && empty($contact_number_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

        // Insert data into the database
        $sql = "INSERT INTO users (first_name, last_name, father_name, contact_number, username, password) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement
            $stmt->bind_param("ssssss", $first_name, $last_name, $father_name, $contact_number, $username, password_hash($password, PASSWORD_DEFAULT));

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("location: login.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css"> <!-- Link to external CSS file -->
</head>
<body>
    <header>
        <div class="container">
            <h1>Create an Account</h1>
        </div>
    </header>

    <main>
        <div class="content-container">
            <h2>Register</h2>
            <p>Please fill this form to create an account.</p>

            <form action="register.php" method="post">
                <!-- First Name -->
                <label for="first_name">First Name:</label><br>
                <input type="text" name="first_name" id="first_name" value="<?php echo $first_name; ?>"><br>
                <span><?php echo $first_name_err; ?></span><br><br>

                <!-- Last Name -->
                <label for="last_name">Last Name:</label><br>
                <input type="text" name="last_name" id="last_name" value="<?php echo $last_name; ?>"><br>
                <span><?php echo $last_name_err; ?></span><br><br>

                <!-- Father's Name -->
                <label for="father_name">Father's Name:</label><br>
                <input type="text" name="father_name" id="father_name" value="<?php echo $father_name; ?>"><br>
                <span><?php echo $father_name_err; ?></span><br><br>

                <!-- Contact Number -->
                <label for="contact_number">Contact Number:</label><br>
                <input type="text" name="contact_number" id="contact_number" value="<?php echo $contact_number; ?>"><br>
                <span><?php echo $contact_number_err; ?></span><br><br>

                <!-- Username -->
                <label for="username">Username:</label><br>
                <input type="text" name="username" id="username" value="<?php echo $username; ?>"><br>
                <span><?php echo $username_err; ?></span><br><br>

                <!-- Password -->
                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password"><br>
                <span><?php echo $password_err; ?></span><br><br>

                <!-- Confirm Password -->
                <label for="confirm_password">Confirm Password:</label><br>
                <input type="password" name="confirm_password" id="confirm_password"><br>
                <span><?php echo $confirm_password_err; ?></span><br><br>

                <input type="submit" value="Create Account">
            </form>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Bank Management System. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
