<?php
$servername = "localhost";
$username = "root";  // Default MySQL username
$password = "";      // Default MySQL password (empty in XAMPP/MAMP)
$dbname = "bank_management";  // The name of the database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
