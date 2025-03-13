<?php
session_start();
include '../db.php'; // Ensure you have a database connection

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Fetch logged-in user details
$loggedInUserID = $_SESSION['userID'];
$sqlUser = "SELECT fname, lname, profile FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $loggedInUserID);
$stmt->execute();
$userResult = $stmt->get_result();
$loggedInUser = $userResult->fetch_assoc();
$stmt->close();

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// SQL query with LIKE operator for searching
$sql = "SELECT userID, fname, lname, email, birthday, role, profile 
        FROM Users 
        WHERE userID LIKE ? 
        OR fname LIKE ? 
        OR lname LIKE ? 
        OR email LIKE ? 
        OR birthday LIKE ? 
        OR role LIKE ?";

$stmt = $conn->prepare($sql);
$searchParam = "%$search%";
$stmt->bind_param("ssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/nav.css">
    <link rel="stylesheet" href="../styles/admin1.css">
    <link rel="stylesheet" href="../styles/search-bar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/menu.js"></script>
    <script src="../script/click.js"></script>
    <title>Admin - Hope for the Strays</title>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <img src="../assets/sidebar/material-symbols_menu-rounded.png" alt="menu" id="menu-icon" height="40">
            </div>
            <div>
                <h1>ADMIN</h1>
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

        <!-- Main Content -->
        <div class="main">
            <!-- Search Bar -->
            <div class="search">
                <form method="GET" action="">
                    <div class="search-input">
                        <img src="../assets/search-bar/ic_round-search.png" alt="">
                        <input type="text" name="search" placeholder="Search Users" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </form>
            </div>

            <!-- User Table -->
            <div class="content">
                <h2>USERS</h2>
                <button class="add-user-btn" onclick="window.location.href='add_user.php'">Add User</button>
                <table class="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th>Role</th>
                        <th>Profile</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['userID']); ?></td>
                            <td><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['profile']); ?>" alt="Profile" width="50"></td>
                            <td class="action">
                                <a href="edit_user.php?id=<?php echo $row['userID']; ?>">
                                    <img src="../assets/admin/Vector.png" alt="Edit">
                                </a>
                                <a href="delete_user.php?id=<?php echo $row['userID']; ?>" onclick="return confirm('Are you sure?');">
                                    <img src="../assets/admin/material-symbols_delete.png" alt="Delete">
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="side-nav">
            <ul>
                <li><a href="../pages/admin.php">USER</a></li>
                <li><a href="../pages/admin_pets.php">PETS</a></li>
                <li><a href="../pages/admin_shelter.php">SHELTERS</a></li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>
