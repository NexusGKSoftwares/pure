<?php
include 'db_connection.php'; // Database connection

$response = array();

$sql = "SELECT * FROM bills"; // Fetch all bills in the database
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $bills = array();
    while ($row = $result->fetch_assoc()) {
        $bills[] = $row;
    }
    $response['success'] = true;
    $response['bills'] = $bills;
} else {
    $response['success'] = false;
    $response['message'] = 'No bills found';
}

echo json_encode($response);
?>
