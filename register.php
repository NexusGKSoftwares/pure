<?php
header('Content-Type: application/json');

include 'db_connection.php'; // Include your database connection

// Retrieve data from the frontend
$email = $_POST['email'];
$password = $_POST['password'];
$name = $_POST['name'];

// Hash the password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert a new user
$sql = "INSERT INTO users (email, password, name) VALUES ('$email', '$hashedPassword', '$name')";

if ($conn->query($sql) === TRUE) {
    // Get the newly created user's ID
    $userId = $conn->insert_id; // Assuming 'id' is the auto-incremented primary key

    echo json_encode([
        "message" => "Registration successful",
        "userId" => $userId,
        "email" => $email,
        "name" => $name
    ]);
} else {
    echo json_encode([
        "message" => "Error: " . $conn->error
    ]);
}

$conn->close();
