<?php
include 'db_connection.php';

$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications
]);
?>
