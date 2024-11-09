<?php
include 'db_connection.php';  // Include the database connection file

// Get the data from POST request
$data = $_POST;
if (empty($data)) {
    // Try to decode JSON if form data is not available
    $data = json_decode(file_get_contents('php://input'), true);
}

// Debugging to check the data
print_r($data);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract required fields from the data
    $user_id = isset($data['user_id']) ? $data['user_id'] : null;
    $payment_amount = isset($data['payment_amount']) ? $data['payment_amount'] : null;
    $payment_method = isset($data['payment_method']) ? $data['payment_method'] : null;
    $payment_date = isset($data['payment_date']) ? $data['payment_date'] : date('Y-m-d');  // Default to current date

    if ($user_id && $payment_amount && $payment_method) {
        // Prepare SQL to insert payment into database
        $sql = "INSERT INTO payments (user_id, payment_amount, payment_method, payment_date)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idss", $user_id, $payment_amount, $payment_method, $payment_date);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Payment recorded successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error recording payment."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Missing required fields."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

// Close the database connection
$conn->close();
?>
