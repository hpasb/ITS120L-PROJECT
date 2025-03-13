<?php
include "../db.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $userID = $_POST["userID"]; // Get current user ID

    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND userID != ?");
    $stmt->bind_param("si", $email, $userID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    echo ($count > 0) ? "exists" : "available";
}
?>
