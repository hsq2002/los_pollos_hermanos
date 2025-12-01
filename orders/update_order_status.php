<?php
include '../db_connect.php'; 

$order_id = $status = '';
$message = '';
$show_form = false;

// --- STEP 1: Fetch Order Status ---
if (isset($_GET['order_id']) || isset($_POST['fetch_order']) || isset($_POST['update_status'])) {
    $order_id = $conn->real_escape_string($_GET['order_id'] ?? $_POST['order_id']);
    
    $sql = "SELECT status FROM Restaurant_Order WHERE order_ID = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $status = $row['status'];
            $show_form = true;
        } else {
            $message = "<p style='color:red;'>Order ID not found.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Error preparing fetch statement: " . $conn->error . "</p>";
    }
}

// --- STEP 2: Process the Update ---
if (isset($_POST['update_status'])) {
    $order_id = $conn->real_escape_string($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);

    if (empty($order_id) || empty($new_status)) {
        $message = "<p style='color:red;'>Order ID and new Status are required!</p>";
        $show_form = true;
    } else {
        $sql = "UPDATE Restaurant_Order SET status = ? WHERE order_ID = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("si", $new_status, $order_id); 

            if ($stmt->execute()) {
                $message = "<p style='color:green;'>Order ID {$order_id} status updated to '{$new_status}' successfully!</p>";
                // Re-fetch the new status
                $_POST['fetch_order'] = true; 
            } else {
                $message = "<p style='color:red;'>Error updating status: " . $stmt->error . "</p>";
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
<head><title>Update Order Status</title></head>
<body>
    <h2>Update Order Status</h2>
    <?php echo $message; ?>

    <form method="POST" action="update_order_status.php">
        <label for="order_id_fetch">Enter Order ID:</label>
        <input type="number" id="order_id_fetch" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>" required>
        <input type="submit" name="fetch_order" value="Load Current Status">
    </form>
    
    <hr>
    
    <?php if ($show_form && $order_id) : ?>
    <h3>Order ID: <?php echo htmlspecialchars($order_id); ?> | Current Status: **<?php echo htmlspecialchars($status); ?>**</h3>
    <form method="POST" action="update_order_status.php">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>"> 

        <label for="new_status">Change Status To:</label>
        <select id="new_status" name="new_status" required>
            <?php 
            $statuses = ['OPEN', 'PREPARING', 'READY', 'COMPLETED', 'CANCELLED'];
            foreach ($statuses as $s) {
                $selected = ($s == $status) ? 'selected' : '';
                echo "<option value='$s' $selected>$s</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" name="update_status" value="Apply Status Change">
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