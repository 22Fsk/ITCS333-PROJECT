<?php
$host = 'localhost';
$dbname = 'dr_schedule';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $course_name = $_POST['course_name'];
    $section_number = $_POST['section_number'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $floor = $_POST['floor'];
    $office_number = $_POST['office_number'];
    $office_start_time = $_POST['office_start_time'];
    $office_end_time = $_POST['office_end_time'];
    $office_day_of_week = $_POST['office_day_of_week'];  

    $professor_id = 1; 
    // Update office information for the professor
    $conn->query("UPDATE professors SET office_number = '$office_number', office_hours_start = '$office_start_time', office_hours_end = '$office_end_time', office_day_of_week = '$office_day_of_week' WHERE id = '$professor_id'");

    // Check if class is already scheduled in the same room and time
    $result = $conn->query("SELECT * FROM schedules WHERE day_of_week = '$day_of_week' AND start_time = '$start_time' AND course_name = '$course_name' AND floor = '$floor'");

    if ($result->num_rows > 0) {
        echo "This class is already scheduled in this room at this time.";
    } else {
        // Insert new schedule if no conflict
        $query = "INSERT INTO schedules (professor_id, course_name, section_number, day_of_week, start_time, end_time, class_number, floor) 
                  VALUES ('$professor_id', '$course_name', '$section_number', '$day_of_week', '$start_time', '$end_time', '$floor')";

        if ($conn->query($query)) {
            echo "Schedule saved successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Fetch existing schedules (for viewing and editing)
$schedules_result = $conn->query("SELECT * FROM schedules");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Schedule</title>

    <!-- Link to CSS file -->
    <link rel="stylesheet" href="schedule.css">

    <script>
        // Function to dynamically change room options based on selected floor for class
        function updateClassRoomOptions() {
            var floor = document.getElementById('class_floor').value;
            var roomSelect = document.getElementById('class_office_number');
            roomSelect.innerHTML = '';  // Clear current options

            var rooms = [];

             // Populate the rooms based on selected floor for class number
    if (floor == 'G') {
        
        for (var i = 1; i <= 99; i++) {
            var roomNumber = ('00' + i).slice(-3); 
            rooms.push(roomNumber);
        }
    } else if (floor == '1st') {
        
        for (var i = 1; i <= 99; i++) {
            var roomNumber = '10' + ('0' + i).slice(-2); 
            rooms.push(roomNumber);
        }
    } else if (floor == '2nd') {
        
        for (var i = 1; i <= 99; i++) {
            var roomNumber = '20' + ('0' + i).slice(-2); 
            rooms.push(roomNumber);
        }
    }


            // Add room options to the select dropdown for class number
            var defaultOption = document.createElement('option');
            defaultOption.textContent = 'Select Class Room';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            roomSelect.appendChild(defaultOption);

            rooms.forEach(function(room) {
                var option = document.createElement('option');
                option.value = room;
                option.textContent = room;
                roomSelect.appendChild(option);
            });
        }
    </script>
</head>
<body>
    <h1>Professor Schedule Management</h1>

    <!-- Schedule Form -->
    <form method="POST">
        <label for="course_name">Course Name:</label>
        <input type="text" id="course_name" name="course_name" required><br>

        <label for="section_number">Section Number:</label>
        <input type="text" id="section_number" name="section_number" required><br>

        <label for="day_of_week">Class Day of the Week:</label>
        <select id="day_of_week" name="day_of_week" required>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
        </select><br>

        <label for="start_time">Class Start Time:</label>
        <input type="time" id="start_time" name="start_time" required><br>

        <label for="end_time">Class End Time:</label>
        <input type="time" id="end_time" name="end_time" required><br>

        <label for="class_floor">Class Floor:</label>
        <select id="class_floor" name="floor" onchange="updateClassRoomOptions()" required>
            <option value="G">Ground Floor</option>
            <option value="1st">1st Floor</option>
            <option value="2nd">2nd Floor</option>
        </select><br>

        <label for="class_office_number">Class Room:</label>
        <select id="class_office_number" name="class_office_number" required></select><br>

        <h3>Professor's Office Information</h3>

        <label for="office_day_of_week">Office Day of the Week:</label>
        <select id="office_day_of_week" name="office_day_of_week" required>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
        </select><br>

        <label for="office_number">Office Number:</label>
        <input type="text" id="office_number" name="office_number" required><br>

        <label for="office_start_time">Office Hours Start Time:</label>
        <input type="time" id="office_start_time" name="office_start_time" required><br>

        <label for="office_end_time">Office Hours End Time:</label>
        <input type="time" id="office_end_time" name="office_end_time" required><br>

        <button type="submit">Save Schedule</button>
    </form>

    <h2>Existing Schedules</h2>
    <table>
        <tr>
            <th>Professor</th>
            <th>Course</th>
            <th>Section</th>
            <th>Day</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Room</th>
            <th>Floor</th>
        </tr>
        <?php while ($schedule = $schedules_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $schedule['professor_id']; ?></td>
                <td><?php echo $schedule['course_name']; ?></td>
                <td><?php echo $schedule['section_number']; ?></td>
                <td><?php echo $schedule['day_of_week']; ?></td>
                <td><?php echo $schedule['start_time']; ?></td>
                <td><?php echo $schedule['end_time']; ?></td>
                <td><?php echo $schedule['floor']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>








