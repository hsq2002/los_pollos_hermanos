<?php 
// Fix the include path by going up one level (../)
include '../db_connect.php'; 
$message = '';

if (isset($_POST['submit'])) {
    // 1. Sanitize and Validate Input
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);

    if (empty($name) || empty($price) || empty($category)) {
        $message = "<p style='color:red;'>Name, Price, and Category are required!</p>";
    } elseif (!is_numeric($price) || $price <= 0) {
        $message = "<p style='color:red;'>Price must be a positive number.</p>";
    } else {
        // 2. Prepare SQL INSERT statement (item_ID is AUTO_INCREMENT)
        $sql = "INSERT INTO Menu_Item (name, price, category) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // sds: string, decimal (double), string
        $stmt->bind_param("sds", $name, $price, $category);

        // 3. Execute with robust error check
        if ($stmt) {
            if ($stmt->execute()) {
                $message = "<p style='color:green;'>New item '{$name}' added successfully!</p>";
            } else {
                $message = "<p style='color:red;'>Error executing query: " . $stmt->error . "</p>";
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
<head><title>Add Menu Item</title></head>
<body>
    <h2>Add New Menu Item</h2>
    <?php echo $message; ?>

    <form method="POST" action="add_item.php">
        <label for="name">Item Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required min="0.01"><br><br>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="">--Select Category--</option>
            <option value="Appetizer">Appetizer</option>
            <option value="Entree">Entree</option>
            <option value="Dessert">Dessert</option>
            <option value="Drink">Drink</option>
        </select><br><br>

        <input type="submit" name="submit" value="Add Item">
    </form>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>
<?php 
if (isset($conn) && $conn) {
    $conn->close();
}
?>