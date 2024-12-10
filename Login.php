<?php
session_start(); 

// Initialize error message variable
$errorMessage = ""; 

// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "uob_database";

// Establish a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate the email format
    if (empty($email) || empty($password)) {
        $errorMessage = "Error: Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Error: Invalid email format.";
    } elseif (substr($email, -11) !== "@uob.edu.bh") {
        $errorMessage = "Error: The email must end with @uob.edu.bh.";
    } else {
        // Check the email and password against the database
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($passwordHash);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $passwordHash)) {
                $_SESSION['email'] = $email; // Store email in session
                header("Location: Homepage.php"); // Redirect to Homepage.php
                exit(); 
            } else {
                $errorMessage = "Error: Incorrect password.";
            }
        } else {
            $errorMessage = "Error: Email not registered.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login - University of Bahrain</title>
    <link rel="stylesheet" href="Login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Login</h1>
            <p>Welcome to the University of Bahrain Doctors' Schedule and Alerts System</p>
            
            <!-- Login Form -->
            <form action="Login.php" method="POST">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <!-- Display error or informational message -->
                <?php if ($errorMessage): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>

                <div class="login-btn-container">
                    <button type="submit" class="login-btn">Login</button>
                </div>
            </form>

            <!-- Registration Link -->
            <p class="register-link">
                Don't have an account? <a href="Registration.php">Register here</a>.
            </p>
        </div>
    </div>
</body>
</html>
