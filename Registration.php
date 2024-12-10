<?php
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

// Initialize variables
$errorMessage = "";
$successMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST["full_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate input
    if (empty($fullName) || empty($email) || empty($password)) {
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif (substr($email, -11) !== "@uob.edu.bh") {
        $errorMessage = "The email must end with @uob.edu.bh.";
    } else {
        // Check if the user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // User already registered, redirect to login page
            header("Location: Login.php?message=already_registered");
            exit();
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Insert the data into the database
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullName, $email, $passwordHash);

            if ($stmt->execute()) {
                $successMessage = "Registration successful! You can now log in.";
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - University of Bahrain</title>
    <link rel="stylesheet" href="Registration.css">
</head>
<body>
    <div class="registration-container">
        <div class="registration-box">
            <h1>Register</h1>
            <p>Join the University of Bahrain Doctors' System</p>
            
            <!-- Registration Form -->
            <form action="registration.php" method="POST">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter a strong password" required>
                
                <!-- Display error message -->
                <?php if ($errorMessage): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>
                
                <!-- Display success message -->
                <?php if ($successMessage): ?>
                    <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
                <?php endif; ?>
                
                <div class="register-btn-container">
                    <button type="submit" class="register-btn">Register</button>
                </div>
            </form>

            <!-- Back to Login Link -->
            <p class="login-link">
                Already have an account? <a href="Login.php">Log in here</a>.
            </p>
        </div>
    </div>
</body>
</html>
