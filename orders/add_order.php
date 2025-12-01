<?php 
include '../db_connect.php'; 
$message = '';
$new_order_id = null;

if (isset($_POST['submit_order'])) {
    // 1. Sanitize Input
    // Note: User_ID is optional for walk-in orders (NULL allowed)
    $user_id = !empty($_POST['user_id']) ? (int)$conn->real_escape_string($_POST['user_id']) : NULL;
    $server_id = (int)$conn->real_escape_string($_POST['server_id']);
    $table_id = (int)$conn->real_escape_string($_POST['table_id']);

    if ($server_id <= 0 || $table_id <= 0) {
        $message = "<p style='color:red;'>Server ID and Table ID are required!</p>";
    } else {
        // 2. Prepare SQL INSERT statement
        $sql = "INSERT INTO Restaurant_Order (User_ID, Server_ID, Table_ID, status) 
                VALUES (?, ?, ?, 'OPEN')";
        $stmt = $conn->prepare($sql);
        
        // This handles the potential NULL for User_ID: it will attempt to bind it as an integer (i).
        // Note: For true NULL support, mysqli sometimes requires complex binding, but we'll use a basic check.
        $stmt->bind_param("iii", $user_id, $server_id, $table_id);

        // 3. Execute with robust error check
        if ($stmt) {
            if ($stmt->execute()) {
                $new_order_id = $conn->insert_id;
                $message = "<p style='color:green;'>Order #{$new_order_id} created successfully! You can now add items.</p>";
            } else {
                $message = "<p style='color:red;'>Error executing query: " . $stmt->error . " (Check if Server/Table ID exist.)</p>";
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
<head><title>Create Order</title></head>
<body>
    <h2>Start New Order</h2>
    <?php echo $message; ?>

    <?php if ($new_order_id): ?>
        <p>Order is open! <a href="add_order_items.php?order_id=<?php echo $new_order_id; ?>">CLICK HERE to add items to Order #<?php echo $new_order_id; ?></a></p>
        <hr>
    <?php endif; ?>

    <form method="POST" action="add_order.php">
        <h3>Order Details</h3>
        <label for="server_id">Server ID:</label>
        <input type="number" id="server_id" name="server_id" required min="1"><br><br>

        <label for="table_id">Table ID:</label>
        <input type="number" id="table_id" name="table_id" required min="1"><br><br>

        <label for="user_id">Customer User ID (Optional):</label>
        <input type="number" id="user_id" name="user_id"><br><br>

        <input type="submit" name="submit_order" value="Create Order">
    </form>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>