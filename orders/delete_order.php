<?php
include '../db_connect.php'; 

$message = '';

if (isset($_POST['delete_order'])) {
    $order_id = $conn->real_escape_string($_POST['order_id']);

    if (empty($order_id)) {
        $message = "<p style='color:red;'>Order ID is required!</p>";
    } else {
        // Prepare SQL DELETE statement
        // Deleting the order record will CASCADE the deletion to the Contains table.
        $sql = "DELETE FROM Restaurant_Order WHERE order_ID = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $order_id); // 'i' for integer

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "<p style='color:green;'>Order ID {$order_id} and all linked items were deleted (voided) successfully!</p>";
                } else {
                    $message = "<p style='color:orange;'>Order ID {$order_id} not found or already deleted.</p>";
                }
            } else {
                $message = "<p style='color:red;'>Error deleting record: " . $stmt->error . "</p>";
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
<head><title>Delete Order (Void)</title></head>
<body>
    <h2>Delete Order (Void)</h2>
    <?php echo $message; ?>

    <p>Warning: Deleting an order will permanently remove all record of its items.</p>

    <form method="POST" action="delete_order.php">
        <label for="order_id">Order ID to Void:</label>
        <input type="number" id="order_id" name="order_id" required><br><br>

        <input type="submit" name="delete_order" value="Delete Order">
    </form>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>