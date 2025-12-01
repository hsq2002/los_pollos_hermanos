<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $sql = "INSERT INTO User (name, phone, role) VALUES ('$name', '$phone', '$role')";
    if ($conn->query($sql) === TRUE) {
        echo "New user added successfully. <a href='index.php'>Go back</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
</head>
<body>
    <h2>Add New User</h2>
    <form method="post" action="">
        <label>Name:</label>
        <input type="text" name="name" required><br><br>

        <label>Phone:</label>
        <input type="text" name="phone"><br><br>

        <label>Role:</label>
        <select name="role">
            <option value="CUSTOMER">Customer</option>
            <option value="SERVER">Server</option>
            <option value="HOST">Host</option>
            <option value="MANAGER">Manager</option>
        </select><br><br>

        <input type="submit" value="Add User">
    </form>
</body>
</html>
