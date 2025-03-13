<?php
session_start();
include '../db.php'; // Ensure this file correctly connects to your database

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php"); // Redirect to login page if not logged in
    exit();
}

// Retrieve user data from the database
$user_id = $_SESSION['userID'];
$query = "SELECT fname, lname, profile FROM users WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $profile_pic);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/nav.css">
    <link rel="stylesheet" href="../styles/home.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/menu.js"></script>
    <script src="../script/click.js"></script>
    <title>Hope for the Strays</title>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <img src="../assets/sidebar/material-symbols_menu-rounded.png" alt="menu" id="menu-icon" height="40">
            </div>
            <div>
                <h1>HOME</h1>
            </div>
        </div>

        <!-- Topbar -->
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

        <!-- Main Content -->
        <div class="main">
            <!-- Welcome Section -->
            <div class="content-container1">
                <img class="welcome-img" src="../assets/home/welcome-img.jpg" alt="Welcome">
                <div class="welcome-text">
                    <h1>Find Your Furry Friend! 🐶🐱</h1>
                    <p>Looking for a furry friend to bring joy into your home? You're in the right place! 
                    Here, you'll find loving stray cats and dogs looking for a second chance at happiness. 
                    Every pet deserves a warm home, and you could be the one to make a difference!</p>
                    <a href="../pages/adopt.php" class="adopt-btn">Browse Available Pets</a>
                </div>
            </div>

            <!-- About Us Section -->
            <div class="content-container">
                <h1>About Us</h1>
                <p>We at Hope for Strays believe that every animal needs to possess the love, care, and the safe house-it calls home. Our mission is to help provide a website to support shelters that rescue, rehabilitate, and rehome stray and abandoned animals, giving them another chance at life. Working with shelters, veterinarians, and kind individuals is a vital part of ensuring every pet receives proper care before they are placed with their forever family.<p><br>
                <p>We happen to be much more than just an adoption service; we are a community made of people who believe in love and possibly make a difference among themselves and for animals. Be it in regard to adoption, volunteering, or donating, your support would be put to use saving lives. Join us on this journey of giving hope to strays, and for one filled with no single animal left behind. </p>
            </div>

            <!-- Announcements Section -->
            <div class="content-container">
                <h1>Announcements</h1>
                <div class="announcement-box">
                    <div class="announcement-text">
                        <p>Each homeless pet has a story—a story of resilience, survival, and hope. These animals have so much love to give, waiting for someone to welcome them home.</p>
                        <p>Imagine how wonderful it feels to see a once-abandoned pet experiencing happiness, care, and love inside your home. 
                        By adopting, you save one life and create an everlasting bond with a loving companion.</p>
                        <p>Be a hero: Give a stray a second chance. 🦸‍♂️🐾</p>
                    </div>
                    <img src="../assets/home/ENCOURAGING_MESSAGE.png" alt="Encouraging Message">
                </div>
            </div>
        </div>

        <!-- Side Navigation -->
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
