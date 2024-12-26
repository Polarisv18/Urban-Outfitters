<?php
$servername = "localhost"; // MAMP uses "localhost"
$username = "root";        // Default username for MAMP
$password = "root";        // Default password for MAMP
$dbname = "Final-Project"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
