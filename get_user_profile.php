<?php
require 'db_connection.php'; // Include the database connection file

// Check if the user_id is provided
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Adjust the SQL query to include only the existing fields
    $stmt = $conn->prepare('SELECT name, email FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'data' => $user
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found'
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'User ID not provided'
    ]);
}

$conn->close();
?>
