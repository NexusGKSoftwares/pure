<?php
// Include your database connection file
include 'db_connection.php';

// Define the rate per unit (e.g., $0.05 per unit)
define('RATE_PER_UNIT', 0.05);

// Check the request method and parameters
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from POST request
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

    // Calculate the units consumed
    $unitsConsumed = $currentReading - $previousReading;

    // Calculate the bill amount
    $amountDue = $unitsConsumed * RATE_PER_UNIT;

    // Debugging: Check calculated units and amount due
    error_log('Calculated Units Consumed: ' . $unitsConsumed . ', Amount Due: ' . $amountDue);

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert into meter_readings table
        $sqlMeter = "INSERT INTO meter_readings (user_id, previous_reading, current_reading, reading_date) 
                     VALUES (?, ?, ?, ?)";
        $stmtMeter = mysqli_prepare($conn, $sqlMeter);
        mysqli_stmt_bind_param($stmtMeter, "iiis", $userId, $previousReading, $currentReading, $readingDate);

        if (!mysqli_stmt_execute($stmtMeter)) {
            throw new Exception('Error inserting meter reading.');
        }
        mysqli_stmt_close($stmtMeter);

        // Insert into bills table with calculated amount
        $dueDate = date('Y-m-d', strtotime('+30 days'));  // Set due date as 30 days from today
        $sqlBill = "INSERT INTO bills (user_id, amount, due_date, payment_status) 
                    VALUES (?, ?, ?, 'Unpaid')";
        $stmtBill = mysqli_prepare($conn, $sqlBill);
        mysqli_stmt_bind_param($stmtBill, "ids", $userId, $amountDue, $dueDate);

        if (!mysqli_stmt_execute($stmtBill)) {
            throw new Exception('Error generating the bill.');
        }
        mysqli_stmt_close($stmtBill);

        // Commit transaction
        mysqli_commit($conn);

        echo json_encode([
            'success' => true,
            'message' => 'Bill generated successfully.',
            'unitsConsumed' => $unitsConsumed,
            'amountDue' => $amountDue
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    // Close connection
    mysqli_close($conn);
}
?>
