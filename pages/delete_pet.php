<?php
session_start();
include '../db.php'; // Database connection

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid pet ID!'); window.location.href='admin_pets.php';</script>";
    exit();
}

$petID = intval($_GET['id']);

// Fetch pet details (to check profile picture)
$petQuery = "SELECT profile FROM Pet WHERE petID = ?";
$stmt = $conn->prepare($petQuery);
$stmt->bind_param("i", $petID);
$stmt->execute();
$petResult = $stmt->get_result();

if ($petResult->num_rows === 0) {
    echo "<script>alert('Pet not found!'); window.location.href='admin_pets.php';</script>";
    exit();
}

$pet = $petResult->fetch_assoc();
$stmt->close();

$defaultProfile = '../assets/adopt/default.jpg';

// Delete pet's profile picture if it's not the default
if ($pet['profile'] !== $defaultProfile && file_exists($pet['profile'])) {
    unlink($pet['profile']);
}

// Delete pet from database
$deleteQuery = "DELETE FROM Pet WHERE petID = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $petID);

if ($stmt->execute()) {
    echo "<script>alert('Pet deleted successfully!'); window.location.href='admin_pets.php';</script>";
} else {
    echo "<script>alert('Error deleting pet!'); window.location.href='admin_pets.php';</script>";
}

$stmt->close();
$conn->close();
?>
