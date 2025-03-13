<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Check if the email exists
    $sql = "SELECT userID, fname, role, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify encrypted password
            if (password_verify($password, $user['password'])) {
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['fname'] = $user['fname'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: pages/admin.php");
                } else {
                    header("Location: pages/home.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password.";
            }
        } else {
            $_SESSION['error'] = "Invalid email or password.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Hope for the Strays</title>
</head>
<body>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            alert("<?php echo $_SESSION['error']; ?>");
        </script>
        <?php unset($_SESSION['error']); ?> <!-- Clear error after displaying -->
    <?php endif; ?>

    <div class="wrapper">
        <div class="container">
            <div class="signin-container">
                <div class="flex-wrapper">
                    <img src="assets/logo.png" alt="logo" height="200">
                </div>
                <h1>Login</h1>
                <form action="" method="POST"> <!-- Form submits to same page -->
                    <div class="input-container">
                        <img src="assets/signin/ic_outline-email.png" alt="">
                        <input id="email" name="email" type="email" placeholder="Email" required>
                    </div>
                    <div class="input-container">
                        <img src="assets/signin/mdi_password-outline.png" alt="">
                        <input id="password" name="password" type="password" placeholder="Password" required>
                    </div>
                    <div class="flex-wrapper">
                        <button class="signin" type="submit">SIGN IN</button>
                    </div>
                </form>
            </div>
            <div class="signup-container">
                <h1>Hello Friend!</h1>
                <p>Don't have an account? Sign up today and be a hero for strays</p>
                <div class="button-wrapper">
                    <button class="signup" onclick="window.location.href='pages/signup.php'">SIGN UP</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
