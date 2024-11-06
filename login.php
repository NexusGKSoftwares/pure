<?php
header('Content-Type: application/json');

include 'db_connection.php'; // Include your database connection

$email = $_POST['email'];
$password = $_POST['password'];

// Query to check user
$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "message" => "Login successful",
        "userId" => $user['id'],
        "email" => $user['email'],
        "name" => $user['name']
    ]);
} else {
    echo json_encode([
        "message" => "Invalid credentials"
    ]);
}

$conn->close();
