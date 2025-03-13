<?php
session_start();
include '../db.php'; // Database connection

// Check if user ID is provided
if (!isset($_GET['id'])) {
    die("Invalid user ID.");
}

$userID = intval($_GET['id']);

// Fetch existing user details
$sql = "SELECT fname, lname, email, birthday, role, profile FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// Function to calculate age
function calculateAge($birthday) {
    $dob = new DateTime($birthday);
    $today = new DateTime();
    return $dob->diff($today)->y;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $birthday = $_POST['birthday'];
    $role = $_POST['role'];
    $newPassword = trim($_POST['password']);

    // Validate age
    if (calculateAge($birthday) < 18) {
        echo "<script>alert('User must be at least 18 years old.');</script>";
    } else {
        // Check if email is already taken (excluding the current user)
        $sql = "SELECT userID FROM Users WHERE email = ? AND userID != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $userID);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo "<script>alert('Email is already taken.');</script>";
        } else {
            $stmt->close();

            // Handle profile image upload
            $profilePath = $user['profile'];
            if (isset($_FILES['profile']) && $_FILES['profile']['size'] > 0) {
                $uploadDir = "../assets/user_profile/";
                $newFilename = "profile_" . $userID . "." . pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
                $uploadFile = $uploadDir . $newFilename;

                // Delete previous profile picture unless it's the default
                if ($user['profile'] !== '../assets/user_profile/default.jpg' && file_exists($user['profile'])) {
                    unlink($user['profile']);
                }

                // Move the new file
                move_uploaded_file($_FILES['profile']['tmp_name'], $uploadFile);
                $profilePath = $uploadFile;
            }

            // Update user data
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $sql = "UPDATE Users SET fname = ?, lname = ?, email = ?, birthday = ?, role = ?, profile = ?, password = ? WHERE userID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssi", $fname, $lname, $email, $birthday, $role, $profilePath, $hashedPassword, $userID);
            } else {
                $sql = "UPDATE Users SET fname = ?, lname = ?, email = ?, birthday = ?, role = ?, profile = ? WHERE userID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssi", $fname, $lname, $email, $birthday, $role, $profilePath, $userID);
            }
            
            if ($stmt->execute()) {
                echo "<script>alert('User updated successfully!'); window.location.href='admin.php';</script>";
            } else {
                echo "<script>alert('Error updating user.');</script>";
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
    <title>Edit User</title>
    <link rel="stylesheet" href="../styles/admin1.css">
    <link rel="stylesheet" href="../styles/edit_user.css">
</head>
<body>
    <div class="edit-user-container">
        <h2>Edit User</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>First Name:</label>
            <input type="text" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>

            <label>Last Name:</label>
            <input type="text" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Birthday:</label>
            <input type="date" name="birthday" value="<?php echo htmlspecialchars($user['birthday']); ?>" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="User" <?php echo ($user['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
            </select>

            <label>New Password (optional):</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">

            <label>Profile Picture:</label>
            <input type="file" name="profile" accept="image/*" onchange="previewImage(event)">
            <p>Current Profile:</p>
            <img id="profile-preview" src="<?php echo htmlspecialchars($user['profile']); ?>" alt="Profile" width="120">

            <button type="submit">Update User</button>
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
