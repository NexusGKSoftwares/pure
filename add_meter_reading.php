<?php
include 'db_connection.php';

// Retrieve data based on content type
$data = $_POST;
if (empty($data)) {
    // Attempt to decode JSON input if no form data is present
    $data = json_decode(file_get_contents('php://input'), true);
}

// Debugging output to check received data
print_r($data);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($data['user_id']) ? $data['user_id'] : null;
    $previous_reading = isset($data['previous_reading']) ? $data['previous_reading'] : null;
    $current_reading = isset($data['current_reading']) ? $data['current_reading'] : null;
    $reading_date = isset($data['reading_date']) ? $data['reading_date'] : null;

    if ($user_id && $previous_reading && $current_reading && $reading_date) {
        $sql = "INSERT INTO meter_readings (user_id, previous_reading, current_reading, reading_date)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $user_id, $previous_reading, $current_reading, $reading_date);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Meter reading added successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error adding meter reading."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Missing required fields."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
$conn->close();
$userId = 123; // Example user ID
$message = "Meter reading submitted by User ID $userId";
$eventType = "meter_reading";

$stmt = $conn->prepare("INSERT INTO notifications (user_id, message, event_type) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $userId, $message, $eventType);
$stmt->execute();
$stmt->close();

?>
