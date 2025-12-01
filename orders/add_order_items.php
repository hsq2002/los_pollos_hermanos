<?php 
include '../db_connect.php'; 
$message = '';

// Get the Order ID from the URL parameter
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (isset($_POST['order_id']) ? (int)$_POST['order_id'] : null);

if (!$order_id) {
    die("Error: No Order ID specified. Please start a new order first.");
}

// ------------------- PROCESS ITEM ADDITION -------------------
if (isset($_POST['add_item'])) {
    // 1. Sanitize Input
    $item_id = (int)$conn->real_escape_string($_POST['item_id']);
    $qty = (int)$conn->real_escape_string($_POST['qty']);
    $notes = $conn->real_escape_string($_POST['notes']);

    // 2. Fetch Item Price
    $price = 0;
    $sql_price = "SELECT price FROM Menu_Item WHERE item_ID = ?";
    $stmt_price = $conn->prepare($sql_price);
    
    if ($stmt_price) {
        $stmt_price->bind_param("i", $item_id);
        $stmt_price->execute();
        $result_price = $stmt_price->get_result();
        if ($row = $result_price->fetch_assoc()) {
            $price = $row['price'];
        }
        $stmt_price->close();
    }

    if ($qty <= 0 || $price == 0) {
        $message = "<p style='color:red;'>Invalid Quantity or Item ID not found!</p>";
    } else {
        // 3. Prepare SQL INSERT into Contains
        $sql = "INSERT INTO Contains (order_ID, item_ID, qty, price, notes) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // iids: integer, integer, integer, decimal (double), string
        $item_total_price = $price * $qty;
        $stmt->bind_param("iiids", $order_id, $item_id, $qty, $item_total_price, $notes);

        // 4. Execute
        if ($stmt) {
            if ($stmt->execute()) {
                $message = "<p style='color:green;'>Added {$qty} item(s) to Order #{$order_id}!</p>";
            } else {
                $message = "<p style='color:red;'>Error adding item: " . $stmt->error . "</p>";
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
<head><title>Add Items to Order</title></head>
<body>
    <h2>Add Items to Order #<?php echo $order_id; ?></h2>
    <?php echo $message; ?>

    <p>Use the Menu Item ID from the Menu Items list to add dishes to this order.</p>
    <form method="POST" action="add_order_items.php?order_id=<?php echo $order_id; ?>">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

        <label for="item_id">Menu Item ID:</label>
        <input type="number" id="item_id" name="item_id" required min="1"><br><br>

        <label for="qty">Quantity:</label>
        <input type="number" id="qty" name="qty" value="1" required min="1"><br><br>

        <label for="notes">Notes/Modification:</label>
        <input type="text" id="notes" name="notes"><br><br>

        <input type="submit" name="add_item" value="Add Item to Order">
    </form>
    
    <hr>
    
    <h3>Current Items on Order #<?php echo $order_id; ?></h3>
    <?php
    // Fetch and display current order items
    if (isset($conn) && $conn) {
        $sql_items = "SELECT C.qty, MI.name, C.price, C.notes 
                      FROM Contains C JOIN Menu_Item MI ON C.item_ID = MI.item_ID
                      WHERE C.order_ID = ?";
        $stmt_items = $conn->prepare($sql_items);
        if ($stmt_items) {
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            $total = 0;
            
            if ($result_items->num_rows > 0) {
                echo '<table border="1" style="border-collapse: collapse;">';
                echo '<tr><th>Qty</th><th>Item Name</th><th>Subtotal</th><th>Notes</th></tr>';
                while($row = $result_items->fetch_assoc()) {
                    $total += $row['price'];
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row["qty"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["name"]) . '</td>';
                    echo '<td>$' . number_format($row["price"], 2) . '</td>';
                    echo '<td>' . htmlspecialchars($row["notes"]) . '</td>';
                    echo '</tr>';
                }
                echo '<tr><td colspan="2" align="right"><b>TOTAL:</b></td><td><b>$' . number_format($total, 2) . '</b></td><td></td></tr>';
                echo '</table>';
            } else {
                echo "<p>No items added to this order yet.</p>";
            }
            $stmt_items->close();
        }
    }
    ?>
    <br><a href="view_orders.php">Finished? View Open Orders</a> | <a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>