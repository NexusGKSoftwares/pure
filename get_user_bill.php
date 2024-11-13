<?php
// Include the database connection file
include('db_connection.php');

// Check if the 'user_id' is passed via the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Prepare the SQL query to fetch the user's bill information
    $query = "SELECT users.id, users.name, bills.amount_due, bills.payment_status, bills.due_date
              FROM users
              JOIN bills ON users.id = bills.user_id
              WHERE users.id = ?";

    // Prepare and bind the parameters
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $user_id); // 'i' stands for integer

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if there are any rows
        if ($result->num_rows > 0) {
            // Initialize an array to store the data
            $bills = [];

            // Fetch the result as an associative array
            while ($row = $result->fetch_assoc()) {
                $bills[] = $row;
            }

            // Return a success response with the bills data
            echo json_encode([
                "success" => true,
                "bills" => $bills
            ]);
        } else {
            // If no bills are found, return an empty response
            echo json_encode([
                "success" => false,
                "message" => "No bills found for this user."
            ]);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Return an error if the query fails
        echo json_encode([
            "success" => false,
            "message" => "Failed to prepare the query."
        ]);
    }
} else {
    // If user_id is not passed, return an error
    echo json_encode([
        "success" => false,
        "message" => "User ID is required."
    ]);
}

// Close the database connection
$conn->close();
?>
