<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connection.php';

// Get data from the POST request
$data = json_decode(file_get_contents("php://input"));

// Assuming you're receiving 'email' and 'password' from the frontend
$email = $data->email;
$password = $data->password;

// Query to check if the email exists in the database
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, check the password
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Successful login
        echo json_encode(["message" => "Login successful", "userId" => $row['userId']]);
    } else {
        // Incorrect password
        echo json_encode(["message" => "Incorrect password"]);
    }
} else {
    // Email not found
    echo json_encode(["message" => "Email not found"]);
}

$conn->close();
?>
