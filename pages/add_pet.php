<?php
session_start();
include '../db.php'; // Database connection

$error = "";
$success = "";

// Fetch available shelters
$shelterQuery = "SELECT shelterID, name FROM Shelter";
$shelterResult = $conn->query($shelterQuery);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $breed = trim($_POST['breed']);
    $size = $_POST['size'];
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $vaccination = trim($_POST['vaccination']);
    $medical_con = trim($_POST['medical_con']);
    $dietary_needs = trim($_POST['dietary_needs']);
    $status = $_POST['status'];
    $shelterID = !empty($_POST['shelterID']) ? $_POST['shelterID'] : NULL;
    $profilePath = '../assets/pets/default.jpg'; // Default profile image

    // Check for duplicate pet name within the same shelter
    $checkQuery = "SELECT petID FROM Pet WHERE name = ? AND shelterID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("si", $name, $shelterID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "A pet with the same name already exists in this shelter.";
    } else {
        $stmt->close();

        // Insert pet first to get petID
        $insertQuery = "INSERT INTO Pet (name, type, breed, size, age, gender, vaccination, medical_con, dietary_needs, status, profile, shelterID)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssissssssi", $name, $type, $breed, $size, $age, $gender, $vaccination, $medical_con, $dietary_needs, $status, $profilePath, $shelterID);

        if ($stmt->execute()) {
            $petID = $stmt->insert_id; // Get newly inserted pet ID
            $stmt->close();

            // Handle file upload
            if (!empty($_FILES['profile']['name'])) {
                $uploadDir = "../assets/pets/";
                $uploadFile = $uploadDir . "pet_" . $petID . ".jpg"; // Rename to petID

                if (move_uploaded_file($_FILES['profile']['tmp_name'], $uploadFile)) {
                    // Update pet profile in database
                    $updateQuery = "UPDATE Pet SET profile = ? WHERE petID = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("si", $uploadFile, $petID);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            echo "<script>alert('Pet added successfully!'); window.location.href='admin_pets.php';</script>";
            exit();
        } else {
            $error = "Error adding pet.";
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
    <title>Add Pet</title>
    <link rel="stylesheet" href="../styles/admin1.css">
    <link rel="stylesheet" href="../styles/add_pet.css">
</head>
<body>
    <div class="add-user-container">
        <h2>Add Pet</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="name">Pet Name:</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="input-group">
                <label for="type">Type:</label>
                <select name="type" id="type" required>
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                     </select>
            </div>

            <div class="input-group">
                <label for="breed">Breed:</label>
                <input type="text" name="breed" id="breed">
            </div>

            <div class="input-group">
                <label for="size">Size:</label>
                <select name="size" id="size" required>
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
            </div>

            <div class="input-group">
                <label for="age">Age:</label>
                <input type="number" name="age" id="age" min="0" required>
            </div>

            <div class="input-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="input-group">
                <label for="vaccination">Vaccination:</label>
                <input type="text" name="vaccination" id="vaccination" value="none">
            </div>

            <div class="input-group">
                <label for="medical_con">Medical Condition:</label>
                <input type="text" name="medical_con" id="medical_con" value="none">
            </div>

            <div class="input-group">
                <label for="dietary_needs">Dietary Needs:</label>
                <input type="text" name="dietary_needs" id="dietary_needs" value="none">
            </div>

            <div class="input-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="available">Available</option>
                    <option value="pending">Pending</option>
                    <option value="adopted">Adopted</option>
                </select>
            </div>

            <div class="input-group">
                <label for="shelterID">Shelter:</label>
                <select name="shelterID" id="shelterID">
                    <?php while ($shelter = $shelterResult->fetch_assoc()): ?>
                        <option value="<?php echo $shelter['shelterID']; ?>">
                            <?php echo htmlspecialchars($shelter['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="input-group">
                <label for="profile">Pet Profile Picture:</label>
                <input type="file" name="profile" id="profile" accept="image/*">
            </div>

            <div class="button-group">
                <button type="submit" class="btn-primary">Add Pet</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='admin_pets.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
