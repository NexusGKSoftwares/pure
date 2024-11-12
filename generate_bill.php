<?php
// Include your database connection file
include 'db_connection.php';

// Debugging: Log received input to check if the data is being sent correctly
error_log('POST Data Received: ' . file_get_contents('php://input'));

// Define the rate per unit (you can adjust this as needed)
define('RATE_PER_UNIT', 10); // For example, 10 units per unit of water

// Check the request method and parameters
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from POST request
    $userId = isset($_POST['userId']) ? $_POST['userId'] : null;
    $previousReading = isset($_POST['previous_reading']) ? $_POST['previous_reading'] : null;
    $currentReading = isset($_POST['current_reading']) ? $_POST['current_reading'] : null;
    $readingDate = isset($_POST['reading_date']) ? $_POST['reading_date'] : null;

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

    // Calculate the amount due
    $amountDue = $unitsConsumed * RATE_PER_UNIT;

    // Set the due date (30 days from now)
    $dueDate = date('Y-m-d', strtotime('+30 days'));

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert the meter reading into the database
        $sql = "INSERT INTO meter_readings (user_id, previous_reading, current_reading, reading_date) 
                VALUES (?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiis", $userId, $previousReading, $currentReading, $readingDate);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Error inserting meter reading.');
            }

            mysqli_stmt_close($stmt);
        } else {
            throw new Exception('Error with meter readings query.');
        }

        // Insert the bill into the bills table
        $sqlBill = "INSERT INTO bills (user_id, amount_due, due_date) 
                    VALUES (?, ?, ?)";
        
        if ($stmtBill = mysqli_prepare($conn, $sqlBill)) {
            mysqli_stmt_bind_param($stmtBill, "ids", $userId, $amountDue, $dueDate);

            if (!mysqli_stmt_execute($stmtBill)) {
                throw new Exception('Error inserting bill details.');
            }

            mysqli_stmt_close($stmtBill);
        } else {
            throw new Exception('Error with bills query.');
        }

        // Commit the transaction
        mysqli_commit($conn);

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Bill generated successfully.'
        ]);

    } catch (Exception $e) {
        // Rollback transaction in case of error
        mysqli_roll_back($conn);

        // Return error response
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
