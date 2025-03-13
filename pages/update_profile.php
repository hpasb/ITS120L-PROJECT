<?php
session_start();
include "../db.php"; // Include database connection

if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['userID'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$birthday = $_POST['birthday'];
$email = $_POST['email'];

// Validate Birthday (Must be 18+)
$today = new DateTime();
$birthDate = new DateTime($birthday);
$age = $today->diff($birthDate)->y;

if ($age < 18) {
    echo "<script>alert('You must be at least 18 years old.'); window.location.href='user_profile.php';</script>";
    exit();
}

// Fetch the current profile picture filename
$stmt = $conn->prepare("SELECT profile FROM users WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($old_profile);
$stmt->fetch();
$stmt->close();

// Check if a new profile picture was uploaded
if (!empty($_FILES["profile_pic"]["name"])) {
    $target_dir = "../assets/user_profile/"; // Directory to save profile pictures
    $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
    $new_file_name = "profile_" . $user_id . "." . $imageFileType;
    $target_file = $target_dir . $new_file_name;

    $allowed_types = ["jpg", "jpeg", "png", "gif"];

    // Validate file type
    if (!in_array($imageFileType, $allowed_types)) {
        echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed.'); window.location.href='user_profile.php';</script>";
        exit();
    }

    // Delete old profile picture (if it exists and is not the default)
    if ($old_profile && file_exists($old_profile) && $old_profile !== "../assets/user_profile/default.jpg") {
        unlink($old_profile);
    }

    // Move the uploaded file and replace existing profile picture
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("UPDATE users SET fname = ?, lname = ?, birthday = ?, email = ?, profile = ? WHERE userID = ?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $birthday, $email, $target_file, $user_id);
    } else {
        echo "<script>alert('Error uploading file.'); window.location.href='user_profile.php';</script>";
        exit();
    }
} else {
    // Update user info without changing profile picture
    $stmt = $conn->prepare("UPDATE users SET fname = ?, lname = ?, birthday = ?, email = ? WHERE userID = ?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $birthday, $email, $user_id);
}

// Execute query and check for success
if ($stmt->execute()) {
    echo "<script>alert('Profile updated successfully!'); window.location.href='user_profile.php';</script>";
} else {
    echo "<script>alert('Error updating profile. Please try again.'); window.location.href='user_profile.php';</script>";
}

$stmt->close();
$conn->close();
?>
