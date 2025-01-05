<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "water-management"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["message" => "Database connection failed: " . $conn->connect_error]));
} else {
    echo json_encode(["message" => "Database connection successful"]);
}
?>
