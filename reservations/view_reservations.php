<?php
// Fix the include path by going up one level (../)
include '../db_connect.php'; 
?>
<!DOCTYPE html>
<html>
<head><title>View Reservations</title></head>
<body>
    <h2>All Reservations</h2>

    <?php
    if (isset($conn) && $conn) {
        // Join Reservation with User and Restaurant_Table to get names/details
        $sql = "SELECT R.Res_ID, U.name AS CustomerName, R.party_size, 
                       R.start_time, R.status, R.Table_ID
                FROM Reservation R
                LEFT JOIN User U ON R.User_ID = U.User_id
                ORDER BY R.start_time DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            // Output data in a table format
            echo '<table border="1" style="border-collapse: collapse;">';
            echo '<tr><th>Res ID</th><th>Customer Name</th><th>Party Size</th><th>Time</th><th>Table ID</th><th>Status</th></tr>';
            
            while($row = $result->fetch_assoc()) {
                // Highlight cancelled reservations
                $style = ($row["status"] == 'CANCELLED') ? 'style="background-color: #fdd; opacity: 0.7;"' : '';
                
                echo '<tr ' . $style . '>';
                echo '<td>' . htmlspecialchars($row["Res_ID"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["CustomerName"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($row["party_size"]) . '</td>';
                echo '<td>' . date('M d, Y h:i A', strtotime($row["start_time"])) . '</td>';
                echo '<td>' . htmlspecialchars($row["Table_ID"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($row["status"]) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No reservations found.</p>";
        }
        $conn->close();
    } else {
        echo "<p style='color:red;'>Database connection failed.</p>";
    }
    ?>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>