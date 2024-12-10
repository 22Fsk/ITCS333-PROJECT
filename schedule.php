<?php
$conn = new mysqli('localhost', 'root', '', 'doctor_schedule');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete schedule entry
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $query = "DELETE FROM doctor_schedule WHERE id = ? AND doctor_id = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: schedule.php");
    exit;
}

// Insert or update schedule entry
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['schedule_id']) && !empty($_POST['schedule_id'])) {
        // Update existing schedule
        $schedule_id = $_POST['schedule_id'];
        $type = $_POST['type'];
        $course_code = $_POST['course_code'] ?? null;
        $day = $_POST['day'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $classroom = $_POST['classroom'] ?? null;
        $section = $_POST['section'] ?? null;
        $room = $_POST['room'] ?? null;

        $query = "UPDATE doctor_schedule 
                  SET type = ?, course_code = ?, day = ?, start_time = ?, end_time = ?, classroom = ?, section = ?, room = ? 
                  WHERE id = ? AND doctor_id = 1";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssi", $type, $course_code, $day, $start_time, $end_time, $classroom, $section, $room, $schedule_id);
        $stmt->execute();
    } else {
        // Insert new schedule
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
    }
    header("Location: schedule.php");
    exit;
}

// Fetch existing schedules
$query = "SELECT * FROM doctor_schedule WHERE doctor_id = 1 ORDER BY day, start_time";
$result = $conn->query($query);

$schedules = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedules[$row['day']][] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Weekly Schedule</title>
    <link rel="stylesheet" href="schedule.css">
</head>
<body>
    <div class="container">
        <h1>Manage Weekly Schedule</h1>
        
        <!-- Form for Adding or Editing Schedule -->
        <form id="schedule-form" action="schedule.php" method="POST">
            <input type="hidden" id="schedule_id" name="schedule_id">
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
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
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

        <!-- Weekly Schedule Display -->
        <div class="week-schedule">
            <h2>Your Weekly Schedule</h2>
            <?php 
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($days as $day): ?>
                <div class="day-column">
                    <div class="day-header"><?= $day ?></div>
                    <?php if (isset($schedules[$day])): ?>
                        <?php foreach ($schedules[$day] as $item): ?>
                            <div class="time-slot <?= $item['type'] ?>">
                                <?= $item['start_time'] ?> - <?= $item['end_time'] ?><br>
                                <?php if ($item['type'] == 'class'): ?>
                                    <?= $item['course_code'] ?> (<?= $item['section'] ?>)<br>Room: <?= $item['classroom'] ?>
                                <?php else: ?>
                                    Office Hour<br>Room: <?= $item['room'] ?>
                                <?php endif; ?>
                                <button onclick="editSchedule(<?= htmlspecialchars(json_encode($item)) ?>)">Edit</button>
                                <a href="schedule.php?delete_id=<?= $item['id'] ?>" onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="time-slot">No events</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function editSchedule(item) {
            document.getElementById('schedule_id').value = item.id;
            document.getElementById('type').value = item.type;
            document.getElementById('day').value = item.day;
            document.getElementById('start_time').value = item.start_time;
            document.getElementById('end_time').value = item.end_time;

            if (item.type === 'class') {
                document.getElementById('course_code').value = item.course_code;
                document.getElementById('classroom').value = item.classroom;
                document.getElementById('section').value = item.section;
                document.getElementById('class-options').style.display = 'block';
                document.getElementById('office-options').style.display = 'none';
            } else {
                document.getElementById('room').value = item.room;
                document.getElementById('class-options').style.display = 'none';
                document.getElementById('office-options').style.display = 'block';
            }
        }

        document.getElementById('type').addEventListener('change', (e) => {
            if (e.target.value === 'class') {
                document.getElementById('class-options').style.display = 'block';
                document.getElementById('office-options').style.display = 'none';
            } else {
                document.getElementById('class-options').style.display = 'none';
                document.getElementById('office-options').style.display = 'block';
            }
        });
    </script>
</body>
</html>
