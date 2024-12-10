<?php
$host = 'localhost';
$dbname = 'schedule';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_title'])) {
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];
    $event_date = isset($_POST['event_date']) ? $_POST['event_date'] : '';
    $event_time = $_POST['event_time'];

   
    $stmt = $pdo->prepare("INSERT INTO schedules (event_title, event_description, event_date, event_time) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$event_title, $event_description, $event_date, $event_time]);

    echo "<p>Event added successfully!</p><br />";
}


if (isset($_POST['update_event'])) {
    $event_id = $_POST['event_id'];
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];
    $event_time = $_POST['event_time'];

    
    $stmt = $pdo->prepare("UPDATE schedules SET event_title = ?, event_description = ?, event_time = ? WHERE id = ?");
    $stmt->execute([$event_title, $event_description, $event_time, $event_id]);

    echo "<p>Event updated successfully!</p><br />";
    echo "<a href='?'>Back to Calendar</a>";
}


if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];

    
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->execute([$event_id]);

    echo "<p>Event deleted successfully!</p><br />";
}


function generateCalendar($month, $year) {
    global $pdo;  

    $year = max(2024, $year);  

  
    $first_day_of_month = strtotime("$year-$month-01");
    $total_days_in_month = date('t', $first_day_of_month);
    $first_weekday = date('w', $first_day_of_month);  

   
    $stmt = $pdo->prepare("SELECT event_date FROM schedules WHERE MONTH(event_date) = ? AND YEAR(event_date) = ?");
    $stmt->execute([$month, $year]);
    $events = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); 
   
    $event_dates = array_flip($events);

    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th colspan='7'>" . date('F Y', $first_day_of_month) . "</th></tr>";
    echo "<tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
          </tr><tr>";

    $current_day = 1;
    for ($i = 0; $i < 6; $i++) { 
        for ($j = 0; $j < 7; $j++) {
            if ($i === 0 && $j < $first_weekday) {
                echo "<td></td>";
            } else {
                if ($current_day <= $total_days_in_month) {
                   
                    $formatted_date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($current_day, 2, '0', STR_PAD_LEFT);

                    
                    $class = isset($event_dates[$formatted_date]) ? 'has-event' : '';

                    echo "<td class='$class'><a href='?date=$formatted_date'>$current_day</a></td>";
                    $current_day++;
                } else {
                    echo "<td></td>";
                }
            }
        }
        echo "</tr>";
        if ($current_day > $total_days_in_month) break;
    }

    echo "</table>";
}


if (isset($_GET['date'])) {
    $date = $_GET['date'];

    
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE event_date = ?");
    $stmt->execute([$date]);
    $events = $stmt->fetchAll();

    echo "<h2>Events for $date</h2>";

    if ($events) {
        foreach ($events as $event) {
            echo "<div><strong>" . htmlspecialchars($event['event_title']) . "</strong><br />";
            echo "<p>" . htmlspecialchars($event['event_description']) . "</p>";
            echo "<p>Time: " . $event['event_time'] . "</p>";
            // Edit and delete buttons
            echo "<a href='?edit_event=" . $event['id'] . "'>Edit</a> | ";
            echo "<a href='?delete_event=" . $event['id'] . "' onclick='return confirm(\"Are you sure you want to delete this event?\")'>Delete</a>";
            echo "</div><hr />";
        }
    } else {
        echo "<p>No events for this day.</p>";
    }
    
    // Event add form
    echo "
        <h3>Add Event for $date</h3>
        <form method='POST'>
            <input type='hidden' name='event_date' value='$date'>
            <label for='event_title'>Event Title:</label><br />
            <input type='text' id='event_title' name='event_title' required><br /><br />
            
            <label for='event_description'>Event Description:</label><br />
            <textarea id='event_description' name='event_description' required></textarea><br /><br />
            
            <label for='event_time'>Event Time:</label><br />
            <input type='time' id='event_time' name='event_time'><br /><br />
            
            <input type='submit' value='Add Event'>
        </form>";

    echo "<br><a href='?'>Back to Calendar</a>";
} elseif (isset($_GET['edit_event'])) {
   
    $event_id = $_GET['edit_event'];
    
    
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if ($event) {
        echo "
            <h3>Edit Event</h3>
            <form method='POST'>
                <input type='hidden' name='event_id' value='" . $event['id'] . "'>
                <label for='event_title'>Event Title:</label><br />
                <input type='text' id='event_title' name='event_title' value='" . htmlspecialchars($event['event_title']) . "' required><br /><br />
                
                <label for='event_description'>Event Description:</label><br />
                <textarea id='event_description' name='event_description' required>" . htmlspecialchars($event['event_description']) . "</textarea><br /><br />
                
                <label for='event_time'>Event Time:</label><br />
                <input type='time' id='event_time' name='event_time' value='" . $event['event_time'] . "'><br /><br />
                
                <input type='submit' name='update_event' value='Update Event'>
            </form>
            <br><a href='?'>Back to Calendar</a>";
    } else {
        echo "<p>Event not found.</p>";
    }
} elseif (isset($_GET['past_events'])) {
    // Fetch and display past events
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE event_date < CURDATE() ORDER BY event_date DESC");
    $stmt->execute();
    $events = $stmt->fetchAll();

    echo "<div class='past-events'>";
    echo "<h2>Past Events</h2>";

    if ($events) {
        foreach ($events as $event) {
            echo "<div><strong>" . htmlspecialchars($event['event_title']) . "</strong><br />";
            echo "<p>" . htmlspecialchars($event['event_description']) . "</p>";
            echo "<p>Date: " . $event['event_date'] . " | Time: " . $event['event_time'] . "</p>";
            echo "</div><hr />";
        }
    } else {
        echo "<p>No past events found.</p>";
    }

    echo "</div><br>";
    echo "<a href='?'>Back to Calendar</a>";
} elseif (isset($_GET['upcoming_events'])) {
   
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE event_date >= CURDATE() ORDER BY event_date ASC");
    $stmt->execute();
    $events = $stmt->fetchAll();

    echo "<div class='upcoming-events'>";
    echo "<h2>Upcoming Events</h2>";

    if ($events) {
        foreach ($events as $event) {
            echo "<div><strong>" . htmlspecialchars($event['event_title']) . "</strong><br />";
            echo "<p>" . htmlspecialchars($event['event_description']) . "</p>";
            echo "<p>Date: " . $event['event_date'] . " | Time: " . $event['event_time'] . "</p>";
            echo "</div><hr />";
        }
    } else {
        echo "<p>No upcoming events found.</p>";
    }

    echo "</div><br>";
    echo "<a href='?'>Back to Calendar</a>";
} else {
   
    $month = isset($_GET['month']) ? $_GET['month'] : date('n');
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
    generateCalendar($month, $year);

    
    $next_month = ($month == 12) ? 1 : $month + 1; 
    $next_year = ($month == 12) ? $year + 1 : $year; 

    $prev_month = ($month == 1) ? 12 : $month - 1; 
    $prev_year = ($month == 1) ? max(2024, $year - 1) : $year; 

    echo "<br><a href='?month=$prev_month&year=$prev_year'>Previous Month</a> | 
          <a href='?month=$next_month&year=$next_year'>Next Month</a>";

    
    echo "<br><a href='?past_events=true'>Past Events</a> | 
          <a href='?upcoming_events=true'>Upcoming Events</a>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Schedule</title>
    
    <link rel="stylesheet" href="events.css">
</head>
<body>
   
</body>
</html>

