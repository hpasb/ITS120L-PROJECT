<?php
session_start();
include '../db.php'; // Database connection

// Fetch logged-in admin details
$loggedInUserID = $_SESSION['userID'];
$sqlUser = "SELECT fname, lname, profile FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $loggedInUserID);
$stmt->execute();
$userResult = $stmt->get_result();
$loggedInUser = $userResult->fetch_assoc();
$stmt->close();

// Fetch all pets from the database
$sql = "SELECT Pet.*, Shelter.name AS shelter_name FROM Pet 
        LEFT JOIN Shelter ON Pet.shelterID = Shelter.shelterID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Pets</title>
    <link rel="stylesheet" href="../styles/nav.css">
    <link rel="stylesheet" href="../styles/admin1.css">
    <link rel="stylesheet" href="../styles/search-bar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/menu.js"></script>
    <script src="../script/click.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <div>
                <img src="../assets/sidebar/material-symbols_menu-rounded.png" alt="menu" id="menu-icon" height="40">
            </div>
            <div>
                <h1>ADMIN</h1>
            </div>
        </div>
        <div class="topbar">
            <div class="logo-container">
                <img src="../assets/logo.png" alt="logo">
                <h2>HOPE FOR STRAYS</h2>
            </div>
            <div class="user">
                <div>
                    <p><?php echo htmlspecialchars($loggedInUser['fname'] . " " . $loggedInUser['lname']); ?></p>
                </div>
                <div class="user-img">
                    <img src="<?php echo htmlspecialchars($loggedInUser['profile']); ?>" alt="Profile Picture">
                </div>
            </div>
            <div>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
        <div class="main">
            <div class="search">
                <div class="search-input">
                    <img src="../assets/search-bar/ic_round-search.png" alt="">
                    <input type="text" id="search-pets" placeholder="Search Pets">
                </div>
            </div>
            <div class="content">
                <h2>PETS</h2>
                <button class="add-user-btn" onclick="window.location.href='add_pet.php'">Add Pet</button>
                <table class="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Breed</th>
                        <th>Size</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Vaccination</th>
                        <th>Medical Condition</th>
                        <th>Dietary Needs</th>
                        <th>Status</th>
                        <th>Shelter</th>
                        <th>Profile</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['petID']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                            <td><?php echo htmlspecialchars($row['breed']); ?></td>
                            <td><?php echo htmlspecialchars($row['size']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['vaccination']); ?></td>
                            <td><?php echo htmlspecialchars($row['medical_con']); ?></td>
                            <td><?php echo htmlspecialchars($row['dietary_needs']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['shelter_name'] ?? 'Unassigned'); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['profile']); ?>" alt="Pet Image"></td>
                            <td class="action">
                                <a href="edit_pet.php?id=<?php echo $row['petID']; ?>">
                                    <img src="../assets/admin/Vector.png" alt="Edit">
                                </a>
                                <a href="delete_pet.php?id=<?php echo $row['petID']; ?>" onclick="return confirm('Are you sure?');">
                                    <img src="../assets/admin/material-symbols_delete.png" alt="Delete">
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
        <div class="side-nav">
            <ul>
                <li><a href="admin.php">USER</a></li>
                <li><a href="admin_pets.php">PETS</a></li>
                <li><a href="admin_shelter.php">SHELTERS</a></li>
            </ul>
        </div>
    </div>
    <script>
        document.getElementById("search-pets").addEventListener("keyup", function(event) {
            let searchValue = event.target.value.toLowerCase();
            let rows = document.querySelectorAll(".admin-table tr:not(:first-child)");

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchValue) ? "" : "none";
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
