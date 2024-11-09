// add_meter_reading.php
<?php
include 'db_connect.php';

$userId = $_POST['userId'];
$currentReading = $_POST['currentReading'];
$date = date("Y-m-d");

// Fetch previous reading
$previousQuery = "SELECT current_reading FROM meter_readings WHERE user_id = ? ORDER BY reading_date DESC LIMIT 1";
$stmt = $conn->prepare($previousQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$previousResult = $stmt->get_result();
$previousReading = $previousResult->fetch_assoc()['current_reading'] ?? 0;

// Insert new meter reading
$query = "INSERT INTO meter_readings (user_id, previous_reading, current_reading, reading_date) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiis", $userId, $previousReading, $currentReading, $date);
$stmt->execute();
echo json_encode(["status" => "success"]);
?>
