// get_user_bill.php
<?php
include 'db_connect.php';

$userId = $_GET['userId'];
$query = "SELECT amount_due, due_date FROM bills WHERE user_id = ? AND payment_status = 'unpaid' LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$bill = $result->fetch_assoc();

echo json_encode($bill);
?>
