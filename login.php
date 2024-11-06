<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file (make sure to update with your actual path)
include('db_connection.php');

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Check if the email and password are provided
if (isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = $data['password'];

    // Prepare the SQL query to check if the user exists
    $sql = "SELECT id, email, password, name FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind the email parameter
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, now check the password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login successful
                echo json_encode([
                    "message" => "Login successful",
                    "userId" => $user['id'],
                    "email" => $user['email'],
                    "name" => $user['name']
                ]);
            } else {
                // Invalid password
                echo json_encode(["message" => "Invalid credentials"]);
            }
        } else {
            // No user found with the provided email
            echo json_encode(["message" => "Invalid credentials"]);
        }

        $stmt->close();
    } else {
        // Error preparing the statement
        echo json_encode(["message" => "Error preparing database query"]);
    }

} else {
    // Missing email or password in the request
    echo json_encode(["message" => "Email and password are required"]);
}

// Close database connection
$conn->close();
?>
