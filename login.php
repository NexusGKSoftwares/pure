<?php
header("Content-Type: application/json");

// Database connection parameters
$servername = "localhost"; // Change this to your server name
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "water_management"; // Database name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['message' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the JSON input
$data = json_decode(file_get_contents("php://input"));

// Validate the input
if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(['message' => 'Email and Password are required']);
    exit();
}

$email = $data->email;
$password = $data->password;

// Prepare and execute the SQL statement
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify the password (assuming you hashed the passwords during registration)
    if (password_verify($password, $user['password'])) {
        // Login successful
        echo json_encode(['message' => 'Login successful', 'user' => $user]);
    } else {
        // Invalid password
        echo json_encode(['message' => 'Incorrect password']);
    }
} else {
    // No user found
    echo json_encode(['message' => 'Invalid email or password']);
}

// Close the connection
$stmt->close();
$conn->close();
?>
