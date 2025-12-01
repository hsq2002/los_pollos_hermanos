<?php 
// Fix the include path by going up one level (../)
include '../db_connect.php'; 
$message = '';

if (isset($_POST['submit'])) {
    // 1. Sanitize Input
    $party_size = (int)$conn->real_escape_string($_POST['party_size']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $user_id = (int)$conn->real_escape_string($_POST['user_id']);
    $table_id = (int)$conn->real_escape_string($_POST['table_id']);

    if (empty($start_time) || $party_size <= 0 || $table_id <= 0) {
        $message = "<p style='color:red;'>All fields are required and valid!</p>";
    } else {
        // 2. Prepare SQL INSERT statement (Res_ID is AUTO_INCREMENT)
        $sql = "INSERT INTO Reservation (party_size, start_time, status, User_ID, Table_ID) 
                VALUES (?, ?, 'SCHEDULED', ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // isii: integer, string (datetime), integer, integer
        $stmt->bind_param("isii", $party_size, $start_time, $user_id, $table_id);

        // 3. Execute with robust error check
        if ($stmt) {
            if ($stmt->execute()) {
                $message = "<p style='color:green;'>Reservation created successfully for {$party_size} people!</p>";
            } else {
                $message = "<p style='color:red;'>Error executing query: " . $stmt->error . " (Check if User ID or Table ID exist.)</p>";
            }
            $stmt->close(); 
        } else {
             $message = "<p style='color:red;'>Error preparing statement: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Create Reservation</title></head>
<body>
    <h2>Create New Reservation</h2>
    <?php echo $message; ?>

    <form method="POST" action="add_reservation.php">
        <label for="user_id">Customer User ID (Required):</label>
        <input type="number" id="user_id" name="user_id" required><br><br>

        <label for="party_size">Party Size:</label>
        <input type="number" id="party_size" name="party_size" required min="1"><br><br>

        <label for="start_time">Date and Time:</label>
        <input type="datetime-local" id="start_time" name="start_time" required><br><br>

        <label for="table_id">Assigned Table ID:</label>
        <input type="number" id="table_id" name="table_id" required><br><br>

        <input type="submit" name="submit" value="Book Reservation">
    </form>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>