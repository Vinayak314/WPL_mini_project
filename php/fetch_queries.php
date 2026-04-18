<?php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'not_authorized']);
    exit();
}

$name = $_SESSION['name'];

$sql = "SELECT * FROM queries ORDER BY submitted_at DESC";
$result = mysqli_query($conn, $sql);

$queries = array();
while ($row = mysqli_fetch_assoc($result)) {
    $queries[] = $row;
}

echo json_encode(['name' => $name, 'queries' => $queries]);

mysqli_close($conn);
?>
