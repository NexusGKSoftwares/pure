<?php
// Include your database connection file
include 'db_connection.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;

    // Check if user_id is provided
    if ($userId === null) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID is required'
        ]);
        exit;
    }

    // Fetch notifications for the specified user
    $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }

    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
