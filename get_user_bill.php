<?php
include 'db_connection.php'; // Include your database connection

header('Content-Type: application/json');

// Check if the user is authenticated (optional step if you need authentication)

// SQL query to fetch bills for all users
$sql = "SELECT users.id, users.name, bills.amount, bills.status, bills.due_date 
        FROM users
        LEFT JOIN bills ON users.id = bills.user_id";

$result = $conn->query($sql);

// Check if any records were fetched
if ($result->num_rows > 0) {
    $bills = [];

    while ($row = $result->fetch_assoc()) {
        $bills[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'amount' => $row['amount'],
            'status' => $row['status'],
            'due_date' => $row['due_date']
        ];
    }

    echo json_encode([
        'success' => true,
        'bills' => $bills
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No bills found.'
    ]);
}

$conn->close();
?>
