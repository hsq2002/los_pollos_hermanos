<?php
include '../db_connect.php'; 

$message = '';

if (isset($_POST['delete_item'])) {
    $item_id = $conn->real_escape_string($_POST['item_id']);

    if (empty($item_id)) {
        $message = "<p style='color:red;'>Item ID is required!</p>";
    } else {
        // Prepare SQL DELETE statement
        // Note: DELETE is RESTRICTED if the item is linked to an order in the 'Contains' table.
        $sql = "DELETE FROM Menu_Item WHERE item_ID = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $item_id); // 'i' for integer

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "<p style='color:green;'>Item ID {$item_id} deleted successfully!</p>";
                } else {
                    $message = "<p style='color:orange;'>Item ID {$item_id} not found or already deleted.</p>";
                }
            } else {
                // Catches Foreign Key Constraint violation (item is in an order)
                if ($conn->errno == 1451) {
                    $message = "<p style='color:red;'>Error: Cannot delete Item ID {$item_id}. It is currently linked to one or more orders.</p>";
                } else {
                    $message = "<p style='color:red;'>Error deleting record: " . $stmt->error . "</p>";
                }
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
<head><title>Delete Menu Item</title></head>
<body>
    <h2>Delete Menu Item</h2>
    <?php echo $message; ?>

    <form method="POST" action="delete_item.php">
        <label for="item_id">Item ID to Delete:</label>
        <input type="number" id="item_id" name="item_id" required><br><br>

        <input type="submit" name="delete_item" value="Delete Menu Item">
    </form>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>