<?php
header('Content-Type: application/json');
require 'db_connection.php'; // Include your database connection

$user_id = 1; // Example user ID; update as needed

$sql = "SELECT name, phoneNumber, email, address, profilePictureUrl FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['error' => 'Failed to retrieve user data']);
}

$stmt->close();
$conn->close();
?>
