<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the values from the POST request
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $previous_reading = isset($_POST['previous_reading']) ? $_POST['previous_reading'] : null;
    $current_reading = isset($_POST['current_reading']) ? $_POST['current_reading'] : null;
    $reading_date = isset($_POST['reading_date']) ? $_POST['reading_date'] : null;

    // Check if required fields are present
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
?>
