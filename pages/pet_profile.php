<?php
session_start();
include '../db.php'; // Ensure database connection is correct

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch logged-in user details
$user_id = $_SESSION['userID'];
$query = "SELECT fname, lname, profile FROM users WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $profile_pic);
$stmt->fetch();
$stmt->close();

// Check if pet ID is provided
if (!isset($_GET['petID'])) {
    header("Location: adopt.php"); // Redirect if no pet ID
    exit();
}

$petID = $_GET['petID'];

// Fetch pet and shelter details
$query = "SELECT p.*, s.name AS shelter_name, s.location AS shelter_location, 
                 s.contact_num AS shelter_contact, s.email AS shelter_email 
          FROM Pet p
          LEFT JOIN Shelter s ON p.shelterID = s.shelterID
          WHERE p.petID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $petID);
$stmt->execute();
$result = $stmt->get_result();
$pet = $result->fetch_assoc();
$stmt->close();
$conn->close();

// If pet not found, redirect back
if (!$pet) {
    header("Location: adopt.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/nav.css">
    <link rel="stylesheet" href="../styles/pet_profile1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/menu.js"></script>
    <script src="../script/click.js"></script>
    <title><?php echo htmlspecialchars($pet['name']); ?> - Profile</title>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <div>
                <img src="../assets/sidebar/material-symbols_menu-rounded.png" alt="menu" id="menu-icon" height="40">
            </div>
            <div>
                <h1>PET PROFILE</h1>
            </div>
        </div>
        <div class="topbar">
            <div class="logo-container">
                <img src="../assets/logo.png" alt="logo">
                <h2>HOPE FOR STRAYS</h2>
            </div>
            <div class="user">
                <div>
                    <p><?php echo htmlspecialchars($first_name . " " . $last_name); ?></p>
                </div>
                <div class="user-img">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                </div>
            </div>
            <div>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>

        <div class="main">
            <a href="adopt.php">
                <img class="back" src="../assets/user_profile/ep_back (1).png" alt="Back" style="height:2rem; width:2rem;">
            </a>
            <div class="grid-container">
                <div class="pet-info">
                    <div class="pet-img">
                        <img src="<?php echo htmlspecialchars($pet['profile']); ?>" alt="Pet Image">
                    </div>
                    <div class="name">
                        <p><span class="bold"><?php echo htmlspecialchars($pet['name']); ?></span></p>
                        <p><?php echo htmlspecialchars(ucfirst($pet['status'])); ?></p>
                    </div>
                    <div class="basic-info">
                        <p><span class="bold">BASIC INFORMATION</span></p>
                        <p>Age: <?php echo htmlspecialchars($pet['age']); ?></p>
                        <p>Type: <?php echo htmlspecialchars($pet['type']); ?></p>
                        <p>Breed: <?php echo htmlspecialchars($pet['breed']); ?></p>
                        <p>Gender: <?php echo htmlspecialchars(ucfirst($pet['gender'])); ?></p>
                        <p>Size/Weight: <?php echo htmlspecialchars($pet['size']); ?></p>
                    </div>
                </div>
                <div class="health-info">
                    <p><span class="bold">Health and Care</span></p>
                    <p>Vaccination: <?php echo htmlspecialchars($pet['vaccination']); ?></p>
                    <p>Medical Condition: <?php echo htmlspecialchars($pet['medical_con']); ?></p>
                    <p>Dietary Needs: <?php echo htmlspecialchars($pet['dietary_needs']); ?></p>
                </div>
                <div class="adoption-info">
                    <p><span class="bold">Shelter Information</span></p>
                    <p>Name: <?php echo htmlspecialchars($pet['shelter_name'] ?? 'N/A'); ?></p>
                    <p>Location: <?php echo htmlspecialchars($pet['shelter_location'] ?? 'N/A'); ?></p>
                    <p>Mobile Number: <?php echo htmlspecialchars($pet['shelter_contact'] ?? 'N/A'); ?></p>
                    <p>Email: <a href="mailto:<?php echo htmlspecialchars($pet['shelter_email'] ?? ''); ?>">
                        <?php echo htmlspecialchars($pet['shelter_email'] ?? 'N/A'); ?></a>
                    </p>
                </div>
            </div>
        </div>

        <div class="side-nav">
            <ul>
                <li><a href="../pages/home.php">HOME</a></li>
                <li><a href="../pages/adopt.php">PETS</a></li>
                <li><a href="../pages/help_adopt.php">HELP ADOPT</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
