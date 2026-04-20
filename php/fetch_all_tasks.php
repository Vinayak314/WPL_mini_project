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

$sql = "SELECT t.*, m.name AS member_name 
        FROM tasks t 
        JOIN team_members m ON t.member_id = m.member_id 
        ORDER BY t.deadline ASC";
$result = mysqli_query($conn, $sql);

$tasks = array();
while ($row = mysqli_fetch_assoc($result)) {
    $tasks[] = $row;
}

echo json_encode(['tasks' => $tasks]);

mysqli_close($conn);
?>
