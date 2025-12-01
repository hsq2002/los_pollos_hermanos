<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['user_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $sql = "UPDATE User SET name='$name', phone='$phone', role='$role' WHERE User_id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "User updated successfully. <a href='index.php'>Go back</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User</title>
</head>
<body>
    <h2>Update User</h2>
    <form method="post" action="">
        <label>User ID:</label>
        <input type="number" name="user_id" required><br><br>

        <label>New Name:</label>
        <input type="text" name="name" required><br><br>

        <label>New Phone:</label>
        <input type="text" name="phone"><br><br>

        <label>New Role:</label>
        <select name="role">
            <option value="CUSTOMER">Customer</option>
            <option value="SERVER">Server</option>
            <option value="HOST">Host</option>
            <option value="MANAGER">Manager</option>
        </select><br><br>

        <input type="submit" value="Update User">
    </form>
</body>
</html>
