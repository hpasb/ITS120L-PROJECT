<?php
session_start();
include '../db.php'; // Database connection

$error = "";
$success = "";
$petID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch pet details
$petQuery = "SELECT * FROM Pet WHERE petID = ?";
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

// Fetch available shelters
$shelterQuery = "SELECT shelterID, name FROM Shelter";
$shelterResult = $conn->query($shelterQuery);

// Handle form submission
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
    $currentProfile = $pet['profile'];
    $defaultProfile = '../assets/adopt/default.jpg';

    // Handle profile picture update
    if (!empty($_FILES['profile']['name'])) {
        $uploadDir = "../assets/pets/";
        $newProfile = $uploadDir . "pet_" . $petID . ".jpg";

        if (move_uploaded_file($_FILES['profile']['tmp_name'], $newProfile)) {
            // Delete previous profile picture if it's not the default
            if ($currentProfile !== $defaultProfile && file_exists($currentProfile)) {
                unlink($currentProfile);
            }
            $currentProfile = $newProfile;
        }
    }

    // Update pet details
    $updateQuery = "UPDATE Pet SET name = ?, type = ?, breed = ?, size = ?, age = ?, gender = ?, vaccination = ?, medical_con = ?, dietary_needs = ?, status = ?, profile = ?, shelterID = ? WHERE petID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssissssssii", $name, $type, $breed, $size, $age, $gender, $vaccination, $medical_con, $dietary_needs, $status, $currentProfile, $shelterID, $petID);

    if ($stmt->execute()) {
        echo "<script>alert('Pet updated successfully!'); window.location.href='admin_pets.php';</script>";
        exit();
    } else {
        $error = "Error updating pet.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet</title>
    <link rel="stylesheet" href="../styles/admin1.css">
    <link rel="stylesheet" href="../styles/add_pet.css">
</head>
<body>
    <div class="add-user-container">
        <h2>Edit Pet</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="name">Pet Name:</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($pet['name']); ?>" required>
            </div>

            <div class="input-group">
                <label for="type">Type:</label>
                <select name="type" id="type" required>
                    <option value="dog" <?php echo ($pet['type'] == 'dog') ? 'selected' : ''; ?>>Dog</option>
                    <option value="cat" <?php echo ($pet['type'] == 'cat') ? 'selected' : ''; ?>>Cat</option>
                </select>
            </div>

            <div class="input-group">
                <label for="breed">Breed:</label>
                <input type="text" name="breed" id="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>">
            </div>

            <div class="input-group">
                <label for="size">Size:</label>
                <select name="size" id="size" required>
                    <option value="small" <?php echo ($pet['size'] == 'small') ? 'selected' : ''; ?>>Small</option>
                    <option value="medium" <?php echo ($pet['size'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="large" <?php echo ($pet['size'] == 'large') ? 'selected' : ''; ?>>Large</option>
                </select>
            </div>

            <div class="input-group">
                <label for="age">Age:</label>
                <input type="number" name="age" id="age" min="0" value="<?php echo $pet['age']; ?>" required>
            </div>

            <div class="input-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="male" <?php echo ($pet['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($pet['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="input-group">
                <label for="vaccination">Vaccination:</label>
                <input type="text" name="vaccination" id="vaccination" value="<?php echo htmlspecialchars($pet['vaccination']); ?>" required>
            </div>

            <div class="input-group">
                <label for="medical_con">Medical Conditions:</label>
                <input type="text" name="medical_con" id="medical_con" value="<?php echo htmlspecialchars($pet['medical_con']); ?>" required>
            </div>

            <div class="input-group">
                <label for="dietary_needs">Dietary Needs:</label>
                <input type="text" name="dietary_needs" id="dietary_needs" value="<?php echo htmlspecialchars($pet['dietary_needs']); ?>" required>
            </div>

            <div class="input-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="available" <?php echo ($pet['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                    <option value="pending" <?php echo ($pet['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="adopted" <?php echo ($pet['status'] == 'adopted') ? 'selected' : ''; ?>>Adopted</option>
                </select>
            </div>

            <div class="input-group">
                <label for="profile">Profile Picture:</label>
                <input type="file" name="profile" id="profile" accept="image/*">
            </div>

            <div class="input-group">
                <label for="shelterID">Shelter:</label>
                <select name="shelterID" id="shelterID">
                    <?php 
                    $shelterResult->data_seek(0); // Reset pointer in case it was used before
                    while ($shelter = $shelterResult->fetch_assoc()): ?>
                        <option value="<?php echo $shelter['shelterID']; ?>" 
                            <?php echo ($pet['shelterID'] == $shelter['shelterID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($shelter['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>


            <div class="button-group">
                <button type="submit" class="btn-primary">Update Pet</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='admin_pets.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
