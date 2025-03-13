<?php
session_start();
include '../db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $birthday = $_POST['birthday']; // Assuming it's a valid date
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Encrypt password

    // Calculate user's age
    $birthDate = new DateTime($birthday);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;

    if ($age < 18) {
        $_SESSION['error'] = "You must be at least 18 years old to sign up.";
        header("Location: signup.php");
        exit();
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "This email is already registered.";
        header("Location: signup.php");
        exit();
    }

    // Insert user data into the database
    $sql = "INSERT INTO users (fname, lname, birthday, email, password, role) VALUES (?, ?, ?, ?, ?, 'user')";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssss", $first_name, $last_name, $birthday, $email, $password);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Signup successful! You can now log in.";
            header("Location: ../index.php");  // Redirect to homepage
            exit();
        } else {
            $_SESSION['error'] = "Signup failed. Please try again.";
            header("Location: signup.php");
            exit();
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
        header("Location: signup.php");
        exit();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/signup.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Hope for the Strays - Signup</title>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="signin-container">
                <h1>Welcome Back!</h1>
                <p>Already have an account? Sign in now to continue your journey.</p>
                <div class="button-wrapper">
                    <button class="signin" onclick="window.location.href='../index.php'">SIGN IN</button>
                </div>
            </div>
            <div class="signup-container">
                <div class="flex-wrapper">
                    <img src="../assets/logo.png" alt="logo" height="100">
                </div>
                <h1>Create Account</h1>
                
                <!-- Pop-up Error Message -->
                <?php if (isset($_SESSION['error'])): ?>
                    <script>
                        alert("<?php echo $_SESSION['error']; ?>");
                    </script>
                    <?php unset($_SESSION['error']); ?> <!-- Remove error after displaying -->
                <?php endif; ?>

                <form action="signup.php" method="POST">
                    <div class="input-container">
                        <img src="../assets/signin/icon-park-outline_edit-name.png" alt="">
                        <input type="text" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="input-container">
                        <img src="../assets/signin/icon-park-outline_edit-name.png" alt="">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                    <div class="input-container">
                        <img src="../assets/signin/ic_outline-email.png" alt="">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-container">
                        <img src="../assets/signin/date.png" alt="">
                        <input type="date" name="birthday" placeholder="Birthday" required>
                    </div>
                    <div class="input-container">
                        <img src="../assets/signin/mdi_password-outline.png" alt="">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="flex-wrapper">
                        <button class="signup" type="submit">SIGN UP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
