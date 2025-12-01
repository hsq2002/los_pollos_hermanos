<?php
include '../db_connect.php'; 

$item_id = $name = $price = $category = '';
$message = '';
$show_form = false;

// --- STEP 1: Fetch Item Data if ID is provided ---
if (isset($_POST['fetch_item']) || isset($_POST['update_item'])) {
    $item_id = $conn->real_escape_string($_POST['item_id']);
    
    $sql = "SELECT item_ID, name, price, category FROM Menu_Item WHERE item_ID = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $name = $row['name'];
            $price = $row['price'];
            $category = $row['category'];
            $show_form = true;
        } else {
            $message = "<p style='color:red;'>Item ID not found.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Error preparing fetch statement: " . $conn->error . "</p>";
    }
}

// --- STEP 2: Process the Update ---
if (isset($_POST['update_item'])) {
    $item_id = $conn->real_escape_string($_POST['item_id']);
    $new_price = $conn->real_escape_string($_POST['new_price']);
    $new_category = $conn->real_escape_string($_POST['new_category']);

    if (empty($item_id) || empty($new_price) || empty($new_category)) {
        $message = "<p style='color:red;'>All fields are required!</p>";
        $show_form = true; // Keep form open if update failed
    } elseif (!is_numeric($new_price) || $new_price <= 0) {
        $message = "<p style='color:red;'>Price must be a positive number.</p>";
        $show_form = true;
    } else {
        $sql = "UPDATE Menu_Item SET price = ?, category = ? WHERE item_ID = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("dsi", $new_price, $new_category, $item_id); // decimal, string, integer

            if ($stmt->execute()) {
                $message = "<p style='color:green;'>Item ID {$item_id} updated successfully!</p>";
                // Re-fetch updated data
                $_POST['fetch_item'] = true;
                $show_form = true; 
            } else {
                $message = "<p style='color:red;'>Error updating record: " . $stmt->error . "</p>";
                $show_form = true;
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
<head><title>Update Menu Item</title></head>
<body>
    <h2>Update Menu Item</h2>
    <?php echo $message; ?>

    <form method="POST" action="update_item.php">
        <label for="item_id_fetch">Enter Item ID to Update:</label>
        <input type="number" id="item_id_fetch" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>" required>
        <input type="submit" name="fetch_item" value="Load Item Data">
    </form>
    
    <hr>
    
    <?php if ($show_form && $item_id) : ?>
    <h3>Editing Item ID: <?php echo htmlspecialchars($item_id); ?> (Name: <?php echo htmlspecialchars($name); ?>)</h3>
    <form method="POST" action="update_item.php">
        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>"> 

        <p>Current Price: $<?php echo number_format($price, 2); ?></p>
        <label for="new_price">New Price:</label>
        <input type="number" step="0.01" id="new_price" name="new_price" value="<?php echo htmlspecialchars($price); ?>" required min="0.01"><br><br>

        <p>Current Category: <?php echo htmlspecialchars($category); ?></p>
        <label for="new_category">New Category:</label>
        <select id="new_category" name="new_category" required>
            <?php 
            $categories = ['Appetizer', 'Entree', 'Dessert', 'Drink'];
            foreach ($categories as $c) {
                $selected = ($c == $category) ? 'selected' : '';
                echo "<option value='$c' $selected>$c</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" name="update_item" value="Apply Update">
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