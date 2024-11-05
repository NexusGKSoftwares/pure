<?php
// Database connection details
$host = 'localhost';       // Your database host (e.g., localhost)
$dbname = "water_management"; // Database name        // Your database name
$username = 'root';        // Your database username
$password = '';            // Your database password (leave empty for default XAMPP setup)

// Create a new connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
