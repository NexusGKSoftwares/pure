<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$host = 'localhost'; // Your database host
$username = 'root'; // Your database username
$password = ''; // Your database password (if no password, leave it empty)
$db_name = 'water_management'; // Your database name

// Create a new database connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check if the connection was successful
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
