<?php 
session_start();

// Redirect to login page if no email session exists
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root"; // Update with your database username
$password = "";     // Update with your database password
$dbname = "uob_database"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current user's email
$email = $_SESSION['email'];

// Fetch user data from the database
$sql = "SELECT full_name, photo FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No user found.");
}

$user = $result->fetch_assoc();

// Extract user details
$profilePhoto = !empty($user['photo']) ? htmlspecialchars($user['photo']) : "default-profile.png"; // Default profile picture if none exists
$fullName = htmlspecialchars($user['full_name']);

// Close the database connection
$conn->close();
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
    <!-- Profile Picture at the top left -->
    <a href="profile.php" class="profile-link">
        <img src="<?php echo $profilePhoto; ?>" alt="Profile Picture" class="profile-pic">
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
                <h2>Welcome, Dr. <?php echo $fullName; ?>!</h2>
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
