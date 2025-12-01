<?php
include '../db_connect.php'; 

$message = '';

if (isset($_POST['delete_res'])) {
    $res_id = $conn->real_escape_string($_POST['res_id']);
    $action = $conn->real_escape_string($_POST['action']);

    if (empty($res_id)) {
        $message = "<p style='color:red;'>Reservation ID is required!</p>";
    } else {
        if ($action == 'cancel') {
            // Best practice: Update the status instead of deleting the history
            $sql = "UPDATE Reservation SET status = 'CANCELLED' WHERE Res_ID = ?";
            $action_desc = "cancelled";
        } else {
            // Permanent DELETE (Only useful if no historical record is needed)
            $sql = "DELETE FROM Reservation WHERE Res_ID = ?";
            $action_desc = "permanently deleted";
        }
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $res_id); // 'i' for integer

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "<p style='color:green;'>Reservation ID {$res_id} successfully {$action_desc}!</p>";
                } else {
                    $message = "<p style='color:orange;'>Reservation ID {$res_id} not found.</p>";
                }
            } else {
                $message = "<p style='color:red;'>Error processing request: " . $stmt->error . "</p>";
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
<head><title>Cancel Reservation</title></head>
<body>
    <h2>Cancel / Delete Reservation</h2>
    <?php echo $message; ?>

    <form method="POST" action="delete_reservation.php">
        <label for="res_id">Reservation ID:</label>
        <input type="number" id="res_id" name="res_id" required><br><br>
        
        <label>Action:</label><br>
        <input type="radio" id="cancel" name="action" value="cancel" checked>
        <label for="cancel">Cancel (Set Status to CANCELLED)</label><br>
        
        <input type="radio" id="delete" name="action" value="delete">
        <label for="delete">Delete Permanently</label><br><br>

        <input type="submit" name="delete_res" value="Process Action">
    </form>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>