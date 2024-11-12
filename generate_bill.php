<?php
// Include your database connection file
include 'db_connection.php';

// Define the rate per unit as a constant
define('RATE_PER_UNIT', 5); // Adjust the rate per unit as needed

// Check the request method and parameters
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the POST request
    $userId = isset($_POST['userId']) ? $_POST['userId'] : null;
    $previousReading = isset($_POST['previous_reading']) ? $_POST['previous_reading'] : null;
    $currentReading = isset($_POST['current_reading']) ? $_POST['current_reading'] : null;
    $readingDate = isset($_POST['reading_date']) ? $_POST['reading_date'] : null;

    // Debugging: Check what data we are receiving
    error_log('Received POST Data: userId=' . $userId . ', previous_reading=' . $previousReading . ', current_reading=' . $currentReading . ', reading_date=' . $readingDate);

    // Check if all required fields are provided
    if ($userId === null || $previousReading === null || $currentReading === null || $readingDate === null) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields (reading details).'
        ]);
        exit;
    }

    // Calculate units consumed and amount due
    $unitsConsumed = $currentReading - $previousReading;
    $amountDue = $unitsConsumed * RATE_PER_UNIT;
    $dueDate = date('Y-m-d', strtotime('+30 days'));

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert into meter_readings table
        $sqlMeter = "INSERT INTO meter_readings (user_id, previous_reading, current_reading, reading_date) 
                     VALUES (?, ?, ?, ?)";
        $stmtMeter = mysqli_prepare($conn, $sqlMeter);
        mysqli_stmt_bind_param($stmtMeter, "iiis", $userId, $previousReading, $currentReading, $readingDate);
        mysqli_stmt_execute($stmtMeter);
        mysqli_stmt_close($stmtMeter);

        // Insert into bills table
        $sqlBill = "INSERT INTO bills (user_id, units_consumed, amount_due, due_date) 
                    VALUES (?, ?, ?, ?)";
        $stmtBill = mysqli_prepare($conn, $sqlBill);
        mysqli_stmt_bind_param($stmtBill, "iiis", $userId, $unitsConsumed, $amountDue, $dueDate);
        mysqli_stmt_execute($stmtBill);
        mysqli_stmt_close($stmtBill);

        // Commit transaction
        mysqli_commit($conn);

        echo json_encode([
            'success' => true,
            'message' => 'Bill generated successfully.',
            'data' => [
                'userId' => $userId,
                'unitsConsumed' => $unitsConsumed,
                'amountDue' => $amountDue,
                'dueDate' => $dueDate
            ]
        ]);

    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        mysqli_rollback($conn);

        echo json_encode([
            'success' => false,
            'message' => 'Error generating the bill: ' . $e->getMessage()
        ]);
    }

    // Close connection
    mysqli_close($conn);
}
