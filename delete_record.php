<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['user_id'];
    $sql = "DELETE FROM User WHERE User_id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "User deleted successfully. <a href='index.php'>Go back</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete User</title>
</head>
<body>
    <h2>Delete User</h2>
    <form method="post" action="">
        <label>User ID:</label>
        <input type="number" name="user_id" required><br><br>

        <input type="submit" value="Delete User">
    </form>
</body>
</html>
