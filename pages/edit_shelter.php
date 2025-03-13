<?php
session_start();
include '../db.php'; // Database connection

// Check if shelter ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid shelter ID.'); window.location.href='admin_shelter.php';</script>";
    exit();
}

$shelterID = $_GET['id'];
$error = "";

// Fetch existing shelter details
$sql = "SELECT * FROM Shelter WHERE shelterID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelterID);
$stmt->execute();
$result = $stmt->get_result();
$shelter = $result->fetch_assoc();
$stmt->close();

if (!$shelter) {
    echo "<script>alert('Shelter not found.'); window.location.href='admin_shelter.php';</script>";
    exit();
}

// Set existing values
$name = $shelter['name'];
$email = $shelter['email'];
$contact_num = $shelter['contact_num'];
$location = $shelter['location'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact_num = trim($_POST['contact_num']);
    $location = trim($_POST['location']);

    // Check for duplicates (excluding the current shelter ID)
    $checkSQL = "SELECT shelterID FROM Shelter WHERE (name = ? OR email = ? OR contact_num = ?) AND shelterID != ?";
    $stmt = $conn->prepare($checkSQL);
    $stmt->bind_param("sssi", $name, $email, $contact_num, $shelterID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Shelter name, email, or contact number already exists.";
    } else {
        $stmt->close();

        // Update shelter details
        $updateSQL = "UPDATE Shelter SET name = ?, email = ?, contact_num = ?, location = ? WHERE shelterID = ?";
        $stmt = $conn->prepare($updateSQL);
        $stmt->bind_param("ssssi", $name, $email, $contact_num, $location, $shelterID);

        if ($stmt->execute()) {
            // Redirect back to admin_shelter.php after successful update
            echo "<script>alert('Shelter details updated successfully!'); window.location.href='admin_shelter.php';</script>";
            exit();
        } else {
            $error = "Error updating shelter details.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Shelter</title>
    <link rel="stylesheet" href="../styles/admin1.css">
    <link rel="stylesheet" href="../styles/edit_shelter.css">
</head>
<body>
    <div class="add-user-container">
        <h2>Edit Shelter</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label for="name">Shelter Name:</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="input-group">
                <label for="contact_num">Contact Number:</label>
                <input type="text" name="contact_num" id="contact_num" value="<?php echo htmlspecialchars($contact_num); ?>" required>
            </div>

            <div class="input-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($location); ?>" required>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-primary">Update Shelter</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='admin_shelter.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
