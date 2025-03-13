<?php
session_start();
include "../db.php"; // Database connection

// Check if user is logged in
$userData = null;
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $userQuery = "SELECT fname, lname, profile FROM Users WHERE userID = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $resultUser = $stmt->get_result();
    
    if ($resultUser->num_rows > 0) {
        $userData = $resultUser->fetch_assoc();
    }
    $stmt->close();
}

// Fetch available pets from the database
$query = "SELECT * FROM Pet WHERE status = 'available'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/nav.css">
    <link rel="stylesheet" href="../styles/adopt1.css">
    <link rel="stylesheet" href="../styles/search-bar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/menu.js"></script>
    <script src="../script/click1.js"></script>
    <title>Hope for the Strays</title>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <div>
                <img src="../assets/sidebar/material-symbols_menu-rounded.png" alt="menu" id="menu-icon" height="40">
            </div>
            <div>
                <h1>ADOPT</h1>
            </div>
        </div>
        <div class="topbar">
            <div class="logo-container">
                <img src="../assets/logo.png" alt="logo">
                <h2>HOPE FOR STRAYS</h2>
            </div>
            <div class="user">
                <div>
                    <p>
                        <?php 
                            if ($userData) {
                                echo htmlspecialchars($userData['fname']) . " " . htmlspecialchars($userData['lname']);
                            } else {
                                echo 'Guest';
                            }
                        ?>
                    </p>
                </div>
                <div class="user-img">
                    <img src="<?php echo ($userData && !empty($userData['profile'])) ? htmlspecialchars($userData['profile']) : '../assets/user_profile/default.jpg'; ?>" alt="Profile Picture">
                </div>
            </div>
            <div>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
        <div class="main">
            <div class="search">
                <div class="search-input">
                    <input type="text" id="search-pets" placeholder="Search for pets...">
                </div>
            </div>
            <div class="content">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card">
                                <a href="pet_profile.php?petID=' . $row['petID'] . '">
                                    <div class="card-img">
                                        <img src="' . htmlspecialchars($row['profile']) . '" alt="Pet Image">
                                    </div>
                                    <div class="card-desc">
                                        <p><span class="bold">' . htmlspecialchars($row['name']) . '</span></p>
                                        <p>Type: ' . htmlspecialchars($row['type']) . '</p>
                                        <p>Breed: ' . (!empty($row['breed']) ? htmlspecialchars($row['breed']) : 'Unknown') . '</p>
                                    </div>
                                </a>
                              </div>';
                    }
                } else {
                    echo '<p>No available pets at the moment.</p>';
                }
                ?>
            </div>
        </div>
        <div class="side-nav">
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="adopt.php">PETS</a></li>
                <li><a href="help_adopt.php">HELP ADOPT</a></li>
            </ul>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#search-pets").on("keyup", function () {
                let value = $(this).val().toLowerCase();
                $(".card").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            $(".search-btn").on("click", function () {
                $("#search-pets").trigger("keyup"); // Ensure search works on button click
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
