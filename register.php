<?php
// Set headers to handle JSON input
header("Content-Type: application/json");

// Read the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data is set and not null
$email = isset($data['email']) ? $data['email'] : null;
$password = isset($data['password']) ? $data['password'] : null;
$name = isset($data['name']) ? $data['name'] : null;

if ($email && $password && $name) {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Your database connection and insertion logic here
    // ...

    // Example response
    $response = [
        "message" => "Registration successful",
        "userId" => 5, // Replace with actual user ID
        "email" => $email,
        "name" => $name
    ];
} else {
    $response = [
        "message" => "Invalid input data"
    ];
}

echo json_encode($response);
?>
