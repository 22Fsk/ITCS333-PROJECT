document.addEventListener("DOMContentLoaded", () => {
    const typeSelect = document.getElementById("type");
    const classOptions = document.getElementById("class-options");
    const officeOptions = document.getElementById("office-options");
    const scheduleDisplay = document.getElementById("schedule-display");

    // Toggle visibility of options based on type
    typeSelect.addEventListener("change", () => {
        if (typeSelect.value === "class") {
            classOptions.style.display = "block";
            officeOptions.style.display = "none";
        } else {
            classOptions.style.display = "none";
            officeOptions.style.display = "block";
        }
    });

    // Fetch and display the schedule
    function loadSchedule() {
        fetch('schedule.php?fetch=1')
            .then(response => response.json())
            .then(data => {
                scheduleDisplay.innerHTML = ''; // Clear existing schedule

                if (data.length === 0) {
                    scheduleDisplay.innerHTML = '<p>No schedule available.</p>';
                    return;
                }

                data.forEach(item => {
                    const scheduleItem = document.createElement('div');
                    scheduleItem.className = `schedule-item ${item.type}`;
                    scheduleItem.innerHTML = `
                        <strong>${item.type === 'class' ? 'Class' : 'Office Hour'}</strong> 
                        (${item.day}, ${item.start_time} - ${item.end_time})
                        ${item.type === 'class' ? `<br>Course: ${item.course_code}, Section: ${item.section}` : `<br>Room: ${item.room}`}
                    `;
                    scheduleDisplay.appendChild(scheduleItem);
                });
            })
            .catch(error => {
                console.error('Error fetching schedule:', error);
                scheduleDisplay.innerHTML = '<p>Error loading schedule.</p>';
            });
    }

    // Load schedule on page load
    loadSchedule();
});
