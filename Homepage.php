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
    <div class="home-container">
        <header>
            <h1>Welcome to the University of Bahrain</h1>
            <p>Doctors' Schedule and Alerts System</p>
        </header>

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

        <footer>
            <p>&copy; 2024 University of Bahrain. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
