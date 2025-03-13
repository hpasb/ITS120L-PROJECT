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
    <link rel="stylesheet" href="../styles/help_adopt1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/menu.js"></script>
    <script src="../script/click.js"></script>
    <title>Help Adopt - Hope for the Strays</title>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <img src="../assets/sidebar/material-symbols_menu-rounded.png" alt="menu" id="menu-icon" height="40">
            </div>
            <div>
                <h1>HELP ADOPT</h1>
            </div>  
        </div>

        <!-- Topbar -->
        <div class="topbar">
            <div class="logo-container">
                <img src="../assets/logo.png" alt="logo">
                <h2>HOPE FOR STRAYS</h2>
            </div>
            <div class="user">
                <p><?php echo htmlspecialchars($first_name . " " . $last_name); ?></p>
                <div class="user-img">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                </div>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        </div>

        <!-- Main Content -->
        <div class="main">
            <div class="content-container">
                <h1>Why Adopt from Us?</h1>
                <p>Every adoption saves an animal from a life of uncertainty, abuse, or neglect.</p>
                <ul>
                    <li><strong>Matching Personality</strong> – We ensure each animal is matched to a compatible home.</li>
                    <li><strong>Support and Guidance</strong> – Post-adoption support ensures a smooth transition.</li>
                    <li><strong>Join a Caring Community</strong> – Become part of a network of animal lovers.</li>
                </ul>
            </div>

            <div class="content-container">
                <h1>How to Adopt a Pet</h1>
                <h2>Step 1: Search for a Pet</h2>
                <p>Visit the Adoption Page and use the search bar to find pets by name, breed, or location.</p>
                
                <h2>Step 2: View the Pet’s Details</h2>
                <p>Each pet’s profile includes their personality, health status, dietary needs, and shelter contact details.</p>
                
                <h2>Step 3: Contact the Shelter</h2>
                <p>Use the contact details on the pet’s profile to ask about the adoption process.</p>
                
                <h2>Step 4: Schedule a Meet-and-Greet</h2>
                <p>Choose between an online or in-person meeting to interact with the pet.</p>
                
                <h2>Step 5: Submit an Adoption Application</h2>
                <p>Fill out the application form with your contact details, living situation, and experience with pets.</p>
                
                <h2>Step 6: Finalizing the Adoption</h2>
                <p>Once approved, sign the adoption agreement and pay any required fees.</p>
                
                <h2>Final Step: Bringing Your New Pet Home!</h2>
                <p>After completing all requirements, welcome your new pet into your home! 🎉🐶🐱</p>
            </div>

            <div class="content-container">
                <h1>Additional Notes</h1>
                <ul>
                    <li>Adoption approval is not guaranteed—shelters prioritize the pet’s best interests.</li>
                    <li>If you’re not approved for a pet, the shelter may recommend a better fit.</li>
                    <li>Some shelters require a home visit before finalizing the adoption.</li>
                    <li>Stay in touch with the shelter for post-adoption support and pet care advice.</li>
                </ul>
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

    <!-- Chatbot -->
    <div class="chatbot-container">
        <button class="chatbot-button" onclick="toggleChatbot()">💬</button>
        <div class="chatbot-modal" id="chatbotModal">
            <div class="chatbot-header">HopeBot - Adoption Assistant</div>
            <div class="chatbot-content" id="chatbotContent"></div>
            <div class="chatbot-input">
                <input type="text" id="chatInput" placeholder="Ask me about adoption...">
                <button onclick="sendChatMessage()">Send</button>
            </div>
        </div>
    </div>

    <script>
        // Toggles the chatbot modal visibility
        function toggleChatbot() {
            var chatbot = document.getElementById("chatbotModal");
            chatbot.style.display = (chatbot.style.display === "none" || chatbot.style.display === "") ? "flex" : "none";
        }

        // Handles sending the message to the Flask API
        function sendChatMessage() {
            var input = document.getElementById("chatInput").value;
            var content = document.getElementById("chatbotContent");

            // Prevent sending empty message
            if (input.trim() === "") {
                // Optional: Display a prompt if the user tries to send an empty message
                content.innerHTML += `<div class="bot-message"><strong>HopeBot:</strong> Please type a message.</div>`;
                content.scrollTop = content.scrollHeight; // Scroll to the latest message
                return;
            }

            // Display the user's message in the chat
            content.innerHTML += `<div class="user-message"><strong>You:</strong> ${input}</div>`;
            document.getElementById("chatInput").value = ""; // Clear the input box
            content.scrollTop = content.scrollHeight; // Scroll to the latest message

            // Send message to Flask API (Ollama)
            fetch("http://localhost:5000/chat", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ message: input })
            })
            .then(response => response.json())
            .then(data => {
                // Display HopeBot's response
                content.innerHTML += `<div class="bot-message"><strong>HopeBot:</strong> ${data.reply}</div>`;
                content.scrollTop = content.scrollHeight; // Scroll to the latest message
            })
            .catch(error => {
                // If error occurs during the API call
                console.error("Error:", error);
                content.innerHTML += `<div class="bot-message"><strong>HopeBot:</strong> Sorry, I'm having trouble responding right now.</div>`;
                content.scrollTop = content.scrollHeight; // Ensure scroll is at the latest message
            });
        }
    </script>

</body>
</html>
