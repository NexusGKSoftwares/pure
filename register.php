<?php
// Include the database connection
require_once 'db_connection.php';

// Set headers to handle JSON input
header("Content-Type: application/json");

// Read the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data is set and not null
$email = isset($data['email']) ? $data['email'] : null;
$password = isset($data['password']) ? $data['password'] : null;
$name = isset($data['name']) ? $data['name'] : null;

if ($email && $password && $name) {
    // Check if the email already exists
    $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists
        $response = ["message" => "Email already exists"];
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        if ($stmt->execute()) {
            $userId = $stmt->insert_id;

            // Insert notification for the user registration
            $message = "New user joined: User ID $userId";
            $eventType = "user_registration";
            $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message, event_type) VALUES (?, ?, ?)");
            $notifStmt->bind_param("iss", $userId, $message, $eventType);
            $notifStmt->execute();
            $notifStmt->close();

            $response = [
                "message" => "Registration successful",
                "userId" => $userId,
                "email" => $email,
                "name" => $name
            ];
        } else {
            $response = ["message" => "Error occurred during registration"];
        }

        $stmt->close();
    }

    $checkStmt->close();
} else {
    $response = ["message" => "Invalid input data"];
}

echo json_encode($response);
$conn->close();
?>
