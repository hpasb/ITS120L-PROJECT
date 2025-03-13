<?php
session_start();
include '../db.php'; // Database connection

// Function to calculate age
function calculateAge($birthday) {
    $dob = new DateTime($birthday);
    $today = new DateTime();
    return $dob->diff($today)->y;
}

$error = ""; // To store validation errors

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $birthday = $_POST['birthday'];
    $role = $_POST['role'];
    $password = trim($_POST['password']);

    // Validate age (Must be 18+)
    if (calculateAge($birthday) < 18) {
        $error = "User must be at least 18 years old.";
    } else {
        // Check if email already exists
        $sql = "SELECT userID FROM Users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $stmt->close();

            // Default profile picture
            $profilePath = "../assets/user_profile/default.jpg";

            // Hash password before storing it
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into database
            $sql = "INSERT INTO Users (fname, lname, email, birthday, role, profile, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $fname, $lname, $email, $birthday, $role, $profilePath, $hashedPassword);

            if ($stmt->execute()) {
                $userID = $stmt->insert_id; // Get new user ID

                // Handle profile image upload
                if (isset($_FILES['profile']) && $_FILES['profile']['size'] > 0) {
                    $uploadDir = "../assets/user_profile/";
                    $newFilename = "profile_" . $userID . "." . pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
                    $uploadFile = $uploadDir . $newFilename;

                    // Move uploaded file
                    if (move_uploaded_file($_FILES['profile']['tmp_name'], $uploadFile)) {
                        $profilePath = $uploadFile;

                        // Update profile in DB
                        $updateSQL = "UPDATE Users SET profile = ? WHERE userID = ?";
                        $updateStmt = $conn->prepare($updateSQL);
                        $updateStmt->bind_param("si", $profilePath, $userID);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                }

                echo "<script>alert('User added successfully!'); window.location.href='admin.php';</script>";
                exit();
            } else {
                $error = "Error adding user.";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="../styles/admin1.css">
    <link rel="stylesheet" href="../styles/add_user.css">
</head>
<body>
    <div class="add-user-container">
        <h2>Add New User</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label>First Name:</label>
            <input type="text" name="fname" required>

            <label>Last Name:</label>
            <input type="text" name="lname" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Birthday:</label>
            <input type="date" name="birthday" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>

            <label>Password:</label>
            <input type="password" name="password" required placeholder="Enter a strong password">

            <label>Profile Picture:</label>
            <input type="file" name="profile" accept="image/*" onchange="previewImage(event)">
            <p>Profile Preview:</p>
            <img id="profile-preview" src="../assets/user_profile/default.jpg" alt="Profile" width="120">

            <button type="submit">Add User</button>
            <a href="admin.php" class="cancel-btn">Cancel</a>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('profile-preview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
