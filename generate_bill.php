// generate_bill.php
<?php
include 'db_connect.php';

$userId = $_POST['userId'];
$ratePerUnit = 150;
$standingCharge = 300;

// Fetch the latest meter readings
$readingQuery = "SELECT previous_reading, current_reading FROM meter_readings WHERE user_id = ? ORDER BY reading_date DESC LIMIT 1";
$stmt = $conn->prepare($readingQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$readingResult = $stmt->get_result()->fetch_assoc();

$consumedUnits = $readingResult['current_reading'] - $readingResult['previous_reading'];
$unitCharge = $consumedUnits * $ratePerUnit;

// Fetch previous balance
$balanceQuery = "SELECT amount_due FROM bills WHERE user_id = ? AND payment_status = 'unpaid' ORDER BY due_date DESC LIMIT 1";
$stmt = $conn->prepare($balanceQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$balanceResult = $stmt->get_result()->fetch_assoc();
$previousBalance = $balanceResult['amount_due'] ?? 0;

$totalAmountDue = $unitCharge + $previousBalance + $standingCharge;

// Insert bill
$dueDate = date("Y-m-d", strtotime("+7 days"));
$insertQuery = "INSERT INTO bills (user_id, amount_due, previous_balance, due_date, generated_date) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iids", $userId, $totalAmountDue, $previousBalance, $dueDate);
$stmt->execute();

echo json_encode(["status" => "success", "amount_due" => $totalAmountDue, "due_date" => $dueDate]);
?>
