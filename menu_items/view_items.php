<?php
// Fix the include path by going up one level (../)
include '../db_connect.php'; 
?>
<!DOCTYPE html>
<html>
<head><title>View Menu Items</title></head>
<body>
    <h2>Menu Items List</h2>

    <?php
    if (isset($conn) && $conn) {
        // 1. Prepare SQL SELECT statement
        $sql = "SELECT item_ID, name, price, category FROM Menu_Item ORDER BY item_ID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            // 2. Output data in a table format
            echo '<table border="1" style="border-collapse: collapse;">';
            echo '<tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th></tr>';
            
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row["item_ID"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["name"]) . '</td>';
                echo '<td>$' . number_format($row["price"], 2) . '</td>';
                echo '<td>' . htmlspecialchars($row["category"]) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No menu items found. Please add some items.</p>";
        }
        $conn->close();
    } else {
        echo "<p style='color:red;'>Database connection failed.</p>";
    }
    ?>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>