<?php
/*
This code is from ChatGPT. I will have to modify this to suit us. 
But at the outset this pretty looks like it does the job.
*/
// Connect to your database
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "tickets_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current date and time
$currentDateTime = time();

// Get list of clients
$sql = "SELECT * FROM clients";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clientId = $row["id"];
        $clientName = $row["name"];
        $businessHoursStart = strtotime($row["business_hours_start"]);
        $businessHoursEnd = strtotime($row["business_hours_end"]);
        $holidays = explode(',', $row["holidays"]);
        
        // Define escalation thresholds for each level (you may retrieve these from the database if necessary)
        $escalationThresholds = [
            1 => $row["escalation_threshold_1"],
            2 => $row["escalation_threshold_2"],
            3 => $row["escalation_threshold_3"],
            4 => $row["escalation_threshold_4"]
        ];

        foreach ($escalationThresholds as $level => $threshold) {
            // Get tickets for this client that need escalation for this level
            $sql = "SELECT * FROM tickets WHERE client_id = $clientId AND status = 'open'";
            $ticketResult = $conn->query($sql);

            if ($ticketResult->num_rows > 0) {
                // Update the status of escalated tickets for this level
                while ($ticketRow = $ticketResult->fetch_assoc()) {
                    $ticketId = $ticketRow["id"];
                    $ticketCreatedAt = strtotime($ticketRow["created_at"]);
                    
                    // Calculate ticket open duration excluding non-working hours and holidays
                    $openDuration = calculateOpenDuration($ticketCreatedAt, $currentDateTime, $businessHoursStart, $businessHoursEnd, $holidays);
                    
                    // Check if open duration exceeds escalation threshold
                    if ($openDuration >= $threshold) {
                        // Perform escalation action, e.g., update ticket status to "escalated"
                        $updateSql = "UPDATE tickets SET status = 'escalated' WHERE id = $ticketId";
                        if ($conn->query($updateSql) === TRUE) {
                            echo "Ticket $ticketId for client $clientName has been escalated to level $level.\n";
                        } else {
                            echo "Error updating ticket $ticketId for client $clientName: " . $conn->error;
                        }
                    }
                }
            } else {
                echo "No tickets need escalation for client $clientName at level $level.\n";
            }
        }
    }
} else {
    echo "No clients found.\n";
}

$conn->close();

// Function to calculate ticket open duration excluding non-working hours and holidays
function calculateOpenDuration($startTime, $endTime, $businessHoursStart, $businessHoursEnd, $holidays) {
    $duration = $endTime - $startTime;
    
    // Exclude non-working hours and holidays from the duration
    $currentTime = $startTime;
    while ($currentTime < $endTime) {
        $currentDate = date('Y-m-d', $currentTime);
        $currentHour = date('H', $currentTime);
        $isHoliday = in_array($currentDate, $holidays);
        if ($currentHour < date('H', $businessHoursStart) || $currentHour >= date('H', $businessHoursEnd) || $isHoliday) {
            $duration -= 3600; // Subtract one hour
        }
        $currentTime += 3600; // Move to the next hour
    }
    
    return max($duration, 0); // Ensure duration is non-negative
}
?>
