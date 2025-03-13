<?php
session_start();
include '../db.php'; // Ensure database connection

// Fetch logged-in admin details
$loggedInUserID = $_SESSION['userID'];
$sqlUser = "SELECT fname, lname, profile FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $loggedInUserID);
$stmt->execute();
$userResult = $stmt->get_result();
$loggedInUser = $userResult->fetch_assoc();
$stmt->close();

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Fetch shelters based on search
$sql = "SELECT shelterID, name, email, contact_num, location FROM Shelter 
        WHERE name LIKE ? OR email LIKE ? OR contact_num LIKE ? OR location LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelter Management</title>
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
            <!-- Search Bar -->
            <div class="search">
                <form method="GET" action="">
                    <div class="search-input">
                        <img src="../assets/search-bar/ic_round-search.png" alt="">
                        <input type="text" name="search" placeholder="Search Shelters" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </form>
            </div>

            <div class="content">
                <h2>SHELTERS</h2>
                <button class="add-user-btn" onclick="window.location.href='add_shelter.php'">Add Shelter</button>
                <table class="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['shelterID']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_num']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td class="action">
                                <a href="edit_shelter.php?id=<?php echo $row['shelterID']; ?>">
                                    <img src="../assets/admin/Vector.png" alt="Edit">
                                </a>
                                <a href="delete_shelter.php?id=<?php echo $row['shelterID']; ?>" onclick="return confirm('Are you sure?');">
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
                <li><a href="admin.php">USERS</a></li>
                <li><a href="../pages/admin_pets.php">PETS</a></li>
                <li><a href="admin_shelter.php">SHELTERS</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
