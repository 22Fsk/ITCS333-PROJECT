<?php
session_start();

// Redirect to login page if no email session exists
if (!isset($_SESSION['email'])) {
    header("Location: Homepage.php");
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
    <div class="container">
        <!-- Header Section -->
        <header>
            <div class="header-left">
                <!-- Profile Link with Profile Picture -->
                <a href="profile.php" class="profile-link">
                    <img src="https://th.bing.com/th/id/R.fa0ca630a6a3de8e33e03a009e406acd?rik=MMtJ1mm73JsM6w&riu=http%3a%2f%2fclipart-library.com%2fimg%2f1905734.png&ehk=iv2%2fLMRQKA2W8JFWCwwq6BdYfKr2FmBAlFys22RmPI8%3d&risl=&pid=ImgRaw&r=0"
                     alt="Profile Picture" class="profile-pic">
                    <span>Profile</span>
                </a>
            </div>
            <div class="header-center">
                <h1>Welcome to the University of Bahrain</h1>
                <p>Doctors' Schedule and Alerts System</p>
            </div>
        </header>

        <!-- Main Section -->
        <main>
            <div class="welcome-message">
                <h2>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['email']); ?>!</h2>
                <p>You have successfully logged in to the system. Choose one of the following options:</p>
            </div>

            <!-- Action Buttons -->
            <div class="button-container">
                <a href="add_alert.php" class="action-btn">Add Alert</a>
                <a href="edit_schedule.php" class="action-btn">Edit Schedule</a>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; 2024 University of Bahrain. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
