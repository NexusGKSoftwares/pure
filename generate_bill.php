<?php
// Include your database connection file
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if 'userId' is passed
    $userId = isset($_GET['userId']) ? $_GET['userId'] : (isset($_POST['userId']) ? $_POST['userId'] : null);

    if ($userId === null) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing userId'
        ]);
        exit;
    }

    // Other required data (You can add other inputs if needed, like readings)
    $previousReading = isset($_POST['previous_reading']) ? $_POST['previous_reading'] : null;
    $currentReading = isset($_POST['current_reading']) ? $_POST['current_reading'] : null;
    $readingDate = isset($_POST['reading_date']) ? $_POST['reading_date'] : null;

    if ($previousReading === null || $currentReading === null || $readingDate === null) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields (reading details).'
        ]);
        exit;
    }

    // Your SQL query to generate the bill
    $sql = "INSERT INTO meter_readings (user_id, previous_reading, current_reading, reading_date) 
            VALUES (?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "iiis", $userId, $previousReading, $currentReading, $readingDate);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'success' => true,
                'message' => 'Bill generated successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error generating the bill.'
            ]);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error with database query.'
        ]);
    }

    // Close connection
    mysqli_close($conn);
}
?>
