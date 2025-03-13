<?php
session_start();
include '../db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['userID'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password from the database
    $query = "SELECT password FROM users WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Check if the entered current password is correct
    if (!password_verify($current_password, $hashed_password)) {
        echo "<script>alert('Incorrect current password!'); window.location.href='user_profile.php';</script>";
        exit();
    }

    // Check if the new passwords match
    if ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match!'); window.location.href='user_profile.php';</script>";
        exit();
    }

    // Hash the new password and update it in the database
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE users SET password = ? WHERE userID = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_hashed_password, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Destroy the session and redirect to login page
    session_destroy();
    echo "<script>alert('Password updated successfully! Please log in again.'); window.location.href='../index.php';</script>";
    exit();
}
?>
