<?php 
session_start();

// Redirect to login page if no email session exists
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the University of Bahrain</title>
    <link rel="stylesheet" href="Homepage.css">
</head>
<body>
    <!-- Profile Picture at the top left, moved a little inside -->
    <a href="profile.php" class="profile-link">
        <img src="https://th.bing.com/th/id/R.fa0ca630a6a3de8e33e03a009e406acd?rik=MMtJ1mm73JsM6w&riu=http%3a%2f%2fclipart-library.com%2fimg%2f1905734.png&ehk=iv2%2fLMRQKA2W8JFWCwwq6BdYfKr2FmBAlFys22RmPI8%3d&risl=&pid=ImgRaw&r=0"
             alt="Profile Picture" class="profile-pic">
    </a>

    <!-- Main Container -->
    <div class="container">
        <!-- Header Section without background -->
        <header>
            <div class="header-center">
                <img src="https://iconape.com/wp-content/files/zj/195381/png/unversity_of_bahrain-logo.png" alt="University of Bahrain Logo" class="university-logo">
                <h1>Welcome to the University of Bahrain</h1>
                <p>Doctors' Schedule and Alerts System</p>
            </div>
        </header>

        <!-- Main Section -->
        <main>
            <div class="welcome-message">
                <h2>Welcome, Dr. <?php 
                   // Get the part of the email before "@uob.edu.bh"
                    $email = $_SESSION['email'];
                    $username = substr($email, 0, strpos($email, '@')); 
                    echo htmlspecialchars($username); 
                ?>!</h2>
                <p>You have successfully logged in to the system. Choose one of the following options:</p>
            </div>

            <!-- Action Buttons -->
            <div class="button-container">
                <a href="events.php" class="action-btn">Add Alert</a>
                <a href="schedule.php" class="action-btn">Edit Schedule</a>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; 2024 University of Bahrain. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
