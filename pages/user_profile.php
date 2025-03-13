<?php
session_start();
include '../db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['userID'];
$query = "SELECT fname, lname, birthday, email, profile FROM users WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $birthday, $email, $profile_pic);
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
    <link rel="stylesheet" href="../styles/user_profile1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/menu.js"></script>
    <script src="../script/click.js"></script>
    <title>Hope for the Strays - Profile</title>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <img src="../assets/sidebar/material-symbols_menu-rounded.png" alt="menu" id="menu-icon" height="40">
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
                    <a href="user_profile.php">
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                    </a>
                </div>
            </div>
            <div>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main">
            <img class="back" src="../assets/user_profile/ep_back (1).png" alt="">

            <!-- User Info Section -->
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                <div class="infos">
                    <p class="green"><strong>Personal Info</strong></p>
                    <div class="personal-info">
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($first_name . " " . $last_name); ?></p>
                        <p><strong>Birthday:</strong> <?php echo date("F j, Y", strtotime($birthday)); ?></p>
                    </div>
                </div>
            </div>

            <!-- Contact Info Section -->
            <div class="contact-info">
                <p class="green">Contact Info</p>
                <div class ="contacts"><p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p></div>
            </div>

            <!-- Buttons Section -->
            <div class="button-group">
                <button class="change-password-btn" onclick="openModal('changePasswordModal')">Change Password</button>
                <button class="edit-profile-btn" onclick="openModal('editProfileModal')">Edit</button>
            </div>
        </div>

        <!-- Change Password Modal -->
        <div id="changePasswordModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal('changePasswordModal')">&times;</span>
                <h2>Change Password</h2>
                <form action="change_password.php" method="POST">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>

                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit" class="modal-submit-btn">Update Password</button>
                </form>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div id="editProfileModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal('editProfileModal')">&times;</span>
                <h2>Edit Profile</h2>
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <label for="profile_pic">Profile Picture</label>
                    <input type="file" id="profile_pic" name="profile_pic">

                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                    <button type="submit" class="modal-submit-btn">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Side Navigation -->
        <div class="side-nav">
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="adopt.php">PETS</a></li>
                <li><a href="help_adopt.php">HELP ADOPT</a></li>
            </ul>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = "flex";
        }

        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector("#editProfileForm").addEventListener("submit", function (event) {
                event.preventDefault(); // Prevent form submission
                
                let birthday = document.getElementById("birthday").value;
                let email = document.getElementById("email").value;
                let userID = "<?php echo $_SESSION['userID']; ?>"; // Current User ID

                if (!isValidAge(birthday)) {
                    alert("You must be at least 18 years old.");
                    return;
                }

                checkEmailDuplicate(email, userID).then(isDuplicate => {
                    if (isDuplicate) {
                        alert("This email is already in use. Please choose another.");
                    } else {
                        event.target.submit(); // Submit the form if validation passes
                    }
                });
            });
        });

        function isValidAge(birthday) {
            let birthDate = new Date(birthday);
            let today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            let monthDiff = today.getMonth() - birthDate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return age >= 18; // User must be 18 or older
        }

        async function checkEmailDuplicate(email, userID) {
            try {
                let response = await fetch("check_email.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "email=" + encodeURIComponent(email) + "&userID=" + encodeURIComponent(userID)
                });

                let result = await response.text();
                return result === "exists";
            } catch (error) {
                console.error("Error checking email:", error);
                return false;
            }
        }
    </script>

</body>
</html>
