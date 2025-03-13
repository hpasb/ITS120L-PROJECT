<?php
session_start();
include '../db.php'; // Database connection

$name = $email = $contact_num = $location = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $contact_num = trim($_POST["contact_num"]);
    $location = trim($_POST["location"]);

    // Validate required fields
    if (empty($name) || empty($email) || empty($contact_num) || empty($location)) {
        $errors[] = "All fields are required.";
    } else {
        // Check for duplicates
        $sqlCheck = "SELECT * FROM Shelter WHERE name = ? OR email = ? OR contact_num = ?";
        $stmt = $conn->prepare($sqlCheck);
        $stmt->bind_param("sss", $name, $email, $contact_num);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Shelter name, email, or contact number already exists.";
        } else {
            // Insert new shelter
            $sqlInsert = "INSERT INTO Shelter (name, email, contact_num, location) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bind_param("ssss", $name, $email, $contact_num, $location);

            if ($stmt->execute()) {
                header("Location: admin_shelter.php?success=Shelter added successfully");
                exit();
            } else {
                $errors[] = "Error adding shelter.";
            }
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
    <title>Add Shelter</title>
    <link rel="stylesheet" href="../styles/add_shelter.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Add Shelter</h2>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <?php foreach ($errors as $error) {
                        echo "<p>$error</p>";
                    } ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
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
                    <button type="submit" class="btn-primary">Add Shelter</button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='admin_shelter.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
