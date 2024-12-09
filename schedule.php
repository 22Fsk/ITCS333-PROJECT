<?php
$conn = new mysqli('localhost', 'root', '', 'doctor_schedule');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $course_code = $_POST['course_code'] ?? null;
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $classroom = $_POST['classroom'] ?? null;
    $section = $_POST['section'] ?? null;
    $room = $_POST['room'] ?? null;

    $query = "INSERT INTO doctor_schedule (doctor_id, type, course_code, day, start_time, end_time, classroom, section, room) 
              VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $type, $course_code, $day, $start_time, $end_time, $classroom, $section, $room);
    $stmt->execute();
    header("Location: schedule.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule</title>
    <link rel="stylesheet" href="schedule.css">
</head>
<body>
    <div class="container">
        <h1>Manage Schedule</h1>
        <form id="schedule-form" action="schedule.php" method="POST">
            <div class="form-group">
                <label for="type">Type:</label>
                <select id="type" name="type" required>
                    <option value="class">Class</option>
                    <option value="office_hour">Office Hour</option>
                </select>
            </div>

            <div id="class-options" class="conditional">
                <div class="form-group">
                    <label for="course_code">Course Code:</label>
                    <input type="text" id="course_code" name="course_code">
                </div>
                <div class="form-group">
                    <label for="classroom">Classroom:</label>
                    <input type="text" id="classroom" name="classroom">
                </div>
                <div class="form-group">
                    <label for="section">Section:</label>
                    <input type="text" id="section" name="section">
                </div>
            </div>

            <div id="office-options" class="conditional">
                <div class="form-group">
                    <label for="room">Room:</label>
                    <input type="text" id="room" name="room">
                </div>
            </div>

            <div class="form-group">
                <label for="day">Day:</label>
                <select id="day" name="day" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>

            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" step="300" required>
            </div>

            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" step="300" required>
            </div>

            <button type="submit">Save Schedule</button>
        </form>

        <div class="schedule">
            <h2>Your Schedule</h2>
            <div id="schedule-display"></div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
