<?php
include '../db_connect.php'; 
?>
<!DOCTYPE html>
<html>
<head><title>Open Orders</title></head>
<body>
    <h2>Open Orders List</h2>

    <?php
    if (isset($conn) && $conn) {
        // Query to calculate the total price for each order
        $sql = "SELECT 
                    RO.order_ID, 
                    U_C.name AS CustomerName, 
                    U_S.name AS ServerName, 
                    RO.Table_ID, 
                    RO.status,
                    SUM(C.price) AS OrderTotal
                FROM Restaurant_Order RO
                LEFT JOIN User U_C ON RO.User_ID = U_C.User_id
                LEFT JOIN User U_S ON RO.Server_ID = U_S.User_id
                LEFT JOIN Contains C ON RO.order_ID = C.order_ID
                WHERE RO.status IN ('OPEN', 'PREPARING', 'READY')
                GROUP BY RO.order_ID
                ORDER BY RO.order_time ASC";
                
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo '<table border="1" style="border-collapse: collapse;">';
            echo '<tr><th>ID</th><th>Table</th><th>Server</th><th>Customer</th><th>Status</th><th>Total</th><th>Actions</th></tr>';
            
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row["order_ID"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["Table_ID"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["ServerName"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($row["CustomerName"] ?? 'Walk-in') . '</td>';
                echo '<td><b>' . htmlspecialchars($row["status"]) . '</b></td>';
                echo '<td>$' . number_format($row["OrderTotal"] ?? 0.00, 2) . '</td>';
                echo '<td><a href="update_order_status.php?order_id=' . $row["order_ID"] . '">Update Status</a> | <a href="add_order_items.php?order_id=' . $row["order_ID"] . '">Add Items</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No open orders found.</p>";
        }
    } else {
        echo "<p style='color:red;'>Database connection failed.</p>";
    }
    ?>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>