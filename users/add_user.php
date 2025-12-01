<!DOCTYPE html>
<html>
<head>
    <title>Add User Record</title>
</head>
<body>
    <h2>Add New User</h2>

    <form method="POST" action="add_user.php">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="phone">Phone (Optional):</label>
        <input type="text" id="phone" name="phone"><br><br>

        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="Customer">Customer</option>
            <option value="Server">Server</option>
            <option value="Host">Host</option>
            <option value="Manager">Manager</option>
        </select><br><br>

        <input type="submit" name="submit" value="Add User">
    </form>

<?php
// PHP logic starts here
include '../db_connect.php';// **NOTE:** This path was the source of the 'No such file or directory' error.

if (isset($_POST['submit'])) {
    // 1. Sanitize and Validate Input
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $role = $conn->real_escape_string($_POST['role']);

    // Simple validation (e.g., check if name is not empty)
    if (empty($name)) {
        echo "<p style='color:red;'>Name is required!</p>";
    } else {
        // 3. Prepare SQL INSERT statement
        $sql = "INSERT INTO User (name, phone, role) VALUES (?, ?, ?)";
        
        // Use prepared statements for security (best practice)
        $stmt = $conn->prepare($sql);
        
        // Bind parameters: 'sss' means three strings (name, phone, role)
        $stmt->bind_param("sss", $name, $phone, $role);

        // 4. Execute the statement
        if ($stmt->execute()) {
            echo "<p style='color:green;'>New User record created successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close(); // **NOTE:** This line was the source of the 'Call to a member function close() on null' error.
    }
}
$conn->close();
?>
</body>
</html>