<?php
// Include your database connection file
include 'db_connection.php';

// Check the request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decode the JSON data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Get the data from the decoded JSON object
    $userId = isset($data['userId']) ? $data['userId'] : null;
    $previousReading = isset($data['previous_reading']) ? $data['previous_reading'] : null;
    $currentReading = isset($data['current_reading']) ? $data['current_reading'] : null;
    $readingDate = isset($data['reading_date']) ? $data['reading_date'] : null;

    // Debugging: Log the received data to check if values are correctly parsed
    error_log('Received Data: userId=' . $userId . ', previous_reading=' . $previousReading . ', current_reading=' . $currentReading . ', reading_date=' . $readingDate);

    // Check if all required fields are provided
    if ($userId === null || $previousReading === null || $currentReading === null || $readingDate === null) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields (reading details).'
        ]);
        exit;
    }

    // SQL query to insert a new meter reading record
    $sql = "INSERT INTO meter_readings (user_id, previous_reading, current_reading, reading_date) 
            VALUES (?, ?, ?, ?)";

    // Prepare and execute the SQL statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "iiis", $userId, $previousReading, $currentReading, $readingDate);

        // Execute the statement and provide response based on result
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

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error with database query.'
        ]);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
