<?php
// Set headers to handle JSON input
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data is set and not null
$email = isset($data['email']) ? $data['email'] : null;
$password = isset($data['password']) ? $data['password'] : null;

if ($email && $password) {
    // Your database connection logic
    // ...

    // Check user credentials from your database
    // (Replace this logic with your own)
    // Example pseudo-code:
    $user = // your logic to fetch user using email and password from the database

    if ($user && password_verify($password, $user['hashed_password'])) {
        // Login successful
        $response = [
            "message" => "Login successful",
            "userId" => $user['id'], // Replace with actual user ID
            "email" => $user['email'], // Replace with actual user email
            "name" => $user['name'], // Replace with actual user name
        ];
    } else {
        // Invalid credentials
        $response = [
            "message" => "Invalid credentials"
        ];
    }
} else {
    $response = [
        "message" => "Invalid input data"
    ];
}

echo json_encode($response);
?>
