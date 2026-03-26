<?php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

$member_id = $_SESSION['member_id'];
$name = $_SESSION['name'];

$sql = "SELECT * FROM tasks WHERE member_id = $member_id ORDER BY deadline ASC";
$result = mysqli_query($conn, $sql);

$tasks = array();
while ($row = mysqli_fetch_assoc($result)) {
    $tasks[] = $row;
}

echo json_encode(['name' => $name, 'tasks' => $tasks]);

mysqli_close($conn);
?>
