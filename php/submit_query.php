<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    $sql = "INSERT INTO queries (name, email, reason) VALUES ('$name', '$email', '$reason')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Message sent successfully!'); window.location.href='../contact.html';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href='../contact.html';</script>";
    }
}

mysqli_close($conn);
?>
