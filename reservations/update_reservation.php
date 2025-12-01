<?php
include '../db_connect.php'; 

$res_id = $party_size = $start_time = $status = $table_id = $user_id = '';
$message = '';
$show_form = false;

// --- STEP 1: Fetch Reservation Data ---
if (isset($_POST['fetch_res']) || isset($_POST['update_res'])) {
    $res_id = $conn->real_escape_string($_POST['res_id']);
    
    $sql = "SELECT Res_ID, party_size, start_time, status, User_ID, Table_ID FROM Reservation WHERE Res_ID = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $res_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $party_size = $row['party_size'];
            // Format datetime for the input field
            $start_time = date('Y-m-d\TH:i', strtotime($row['start_time'])); 
            $status = $row['status'];
            $table_id = $row['Table_ID'];
            $user_id = $row['User_ID'];
            $show_form = true;
        } else {
            $message = "<p style='color:red;'>Reservation ID not found.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Error preparing fetch statement: " . $conn->error . "</p>";
    }
}

// --- STEP 2: Process the Update ---
if (isset($_POST['update_res'])) {
    $res_id = $conn->real_escape_string($_POST['res_id']);
    $new_party_size = (int)$conn->real_escape_string($_POST['new_party_size']);
    $new_start_time = $conn->real_escape_string($_POST['new_start_time']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    $new_table_id = (int)$conn->real_escape_string($_POST['new_table_id']);

    if (empty($res_id) || empty($new_start_time) || $new_party_size <= 0) {
        $message = "<p style='color:red;'>All required fields must be valid!</p>";
        $show_form = true;
    } else {
        $sql = "UPDATE Reservation SET party_size = ?, start_time = ?, status = ?, Table_ID = ? WHERE Res_ID = ?";
        $stmt = $conn->prepare($sql);
        
        // isisi: integer, string(datetime), string(enum), integer, integer
        if ($stmt) {
            $stmt->bind_param("isiii", $new_party_size, $new_start_time, $new_status, $new_table_id, $res_id); 

            if ($stmt->execute()) {
                $message = "<p style='color:green;'>Reservation ID {$res_id} updated successfully!</p>";
                // Force a re-fetch to display the updated data in the form
                $_POST['fetch_res'] = true; 
            } else {
                $message = "<p style='color:red;'>Error updating record: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            $message = "<p style='color:red;'>Error preparing update statement: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Update Reservation</title></head>
<body>
    <h2>Update Reservation</h2>
    <?php echo $message; ?>

    <form method="POST" action="update_reservation.php">
        <label for="res_id_fetch">Enter Reservation ID:</label>
        <input type="number" id="res_id_fetch" name="res_id" value="<?php echo htmlspecialchars($res_id); ?>" required>
        <input type="submit" name="fetch_res" value="Load Reservation Data">
    </form>
    
    <hr>
    
    <?php if ($show_form && $res_id) : ?>
    <h3>Editing Reservation ID: <?php echo htmlspecialchars($res_id); ?> (Customer: <?php echo htmlspecialchars($user_id); ?>)</h3>
    <form method="POST" action="update_reservation.php">
        <input type="hidden" name="res_id" value="<?php echo htmlspecialchars($res_id); ?>"> 

        <label for="new_party_size">New Party Size:</label>
        <input type="number" id="new_party_size" name="new_party_size" value="<?php echo htmlspecialchars($party_size); ?>" required min="1"><br><br>

        <label for="new_start_time">New Date and Time:</label>
        <input type="datetime-local" id="new_start_time" name="new_start_time" value="<?php echo htmlspecialchars($start_time); ?>" required><br><br>

        <label for="new_table_id">New Table ID:</label>
        <input type="number" id="new_table_id" name="new_table_id" value="<?php echo htmlspecialchars($table_id); ?>" required min="1"><br><br>

        <label for="new_status">New Status:</label>
        <select id="new_status" name="new_status" required>
            <?php 
            $statuses = ['SCHEDULED', 'CONFIRMED', 'COMPLETED', 'CANCELLED'];
            foreach ($statuses as $s) {
                $selected = ($s == $status) ? 'selected' : '';
                echo "<option value='$s' $selected>$s</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" name="update_res" value="Apply Update">
    </form>
    <?php endif; ?>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>