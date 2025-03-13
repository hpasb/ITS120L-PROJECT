<?php
session_start();
include '../db.php'; // Database connection

// Check if shelter ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid shelter ID.'); window.location.href='admin_shelter.php';</script>";
    exit();
}

$shelterID = $_GET['id'];

// Prepare and execute the delete query
$sql = "DELETE FROM Shelter WHERE shelterID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelterID);

if ($stmt->execute()) {
    echo "<script>alert('Shelter deleted successfully!'); window.location.href='admin_shelter.php';</script>";
} else {
    echo "<script>alert('Error deleting shelter.'); window.location.href='admin_shelter.php';</script>";
}

$stmt->close();
$conn->close();
?>
