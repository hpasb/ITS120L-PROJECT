<?php
session_start();
include '../db.php'; // Database connection

// Check if user ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$userID = intval($_GET['id']);

// Prevent self-deletion (if admin is logged in)
if (isset($_SESSION['userID']) && $_SESSION['userID'] == $userID) {
    echo "<script>alert('You cannot delete your own account.'); window.location.href='admin.php';</script>";
    exit();
}

// Fetch user details to get profile image path
$sql = "SELECT profile FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='admin.php';</script>";
    exit();
}

// Delete profile picture (unless it's the default)
if ($user['profile'] !== '../assets/user_profile/default.jpg' && file_exists($user['profile'])) {
    unlink($user['profile']);
}

// Delete user from the database
$sql = "DELETE FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);

if ($stmt->execute()) {
    echo "<script>alert('User deleted successfully!'); window.location.href='admin.php';</script>";
} else {
    echo "<script>alert('Error deleting user.'); window.location.href='admin.php';</script>";
}

$stmt->close();
$conn->close();
?>
