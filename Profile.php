<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login if the session is not set
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
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

// Fetch user data
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No user found.");
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newDepartment = $_POST['department'];
    $uploadedPhoto = $_FILES['photo'];

    // Keep the current photo if no new photo is uploaded
    $photoPath = $user['photo']; 

    // Handle photo upload if a file is selected
    if ($uploadedPhoto['error'] == UPLOAD_ERR_OK) {
        $photoDirectory = "uploads/"; // Directory to store uploaded photos
        if (!is_dir($photoDirectory)) {
            mkdir($photoDirectory, 0777, true); // Create the directory if it doesn't exist
        }

        $photoPath = $photoDirectory . basename($uploadedPhoto['name']);
        move_uploaded_file($uploadedPhoto['tmp_name'], $photoPath); // Save the uploaded file
    }

    // Update the database
    $update_sql = "UPDATE users SET username = ?, email = ?, department = ?, photo = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssss", $newUsername, $newEmail, $newDepartment, $photoPath, $email);

    if ($update_stmt->execute()) {
        $_SESSION['email'] = $newEmail; // Update session email if changed
        header("Location: Profile.php");
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="Profile.css">
</head>
<body>
    <div class="profile-container">
        <h1>Profile Information</h1>
        <form action="Profile.php" method="POST" enctype="multipart/form-data">
            <!-- Username -->
            <div class="profile-info">
                <label for="username">Full Name:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <!-- Email -->
            <div class="profile-info">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <!-- Department -->
            <div class="profile-info">
                <label for="department">Department:</label>
                <select name="department" id="department" required>
                    <option value="CS" <?php echo ($user['department'] == 'CS') ? 'selected' : ''; ?>>Computer Science</option>
                    <option value="IT" <?php echo ($user['department'] == 'IT') ? 'selected' : ''; ?>>Information Technology</option>
                    <option value="CE" <?php echo ($user['department'] == 'CE') ? 'selected' : ''; ?>>Computer Engineer</option>
                </select>
            </div>

            <!-- Profile Photo -->
            <div class="profile-info">
                <label for="photo">Profile Photo:</label>
                <input type="file" name="photo" id="photo" onchange="previewImage(event)">
                <img id="photo-preview" class="preview" src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Photo">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="update-btn">Update Profile</button>
        </form>

        <a href="Logout.php">Logout</a>
    </div>

    <script src="Profile.js"></script>
</body>
</html>
