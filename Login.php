<?php
session_start(); // Start the session to store user data

// Initialize error message variable
$errorMessage = "";

// Check if the form is submitted and email is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = $_POST["email"];

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Error: Invalid email format.";
    }
    // Check if the email ends with @uob.edu.bh
    elseif (substr($email, -12) !== "@uob.edu.bh") {
        $errorMessage = "Error: The email must end with @uob.edu.bh";
    } else {
        // If everything is valid, store the email in session and redirect to the home page
        $_SESSION['email'] = $email; // Store email in session
        header("Location: Homepage.php"); // Redirect to Homepage.php
        exit(); // Make sure the script stops after the redirect
    }
}
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
            <form action="login.php" method="POST">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                
                <!-- Display error message if set -->
                <?php if ($errorMessage): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>

                <div class="login-btn-container">
                    <button type="submit" class="login-btn">Login with Email</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
