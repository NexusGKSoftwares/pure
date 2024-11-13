<?php
// Include the database connection file
include 'db_connection.php';

// Check the connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Fetch users from the database
$sql = "SELECT id, name, email FROM users"; // Modify fields based on your actual table structure
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    $users = [];

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Return users as JSON
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No users found'
    ]);
}

// Close the database connection
$conn->close();
?>
