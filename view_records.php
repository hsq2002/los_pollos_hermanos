<?php
include 'db_connect.php';
$sql = "SELECT * FROM User";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
</head>
<body>
    <h2>All Users</h2>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Role</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row['User_id']."</td>
                        <td>".$row['name']."</td>
                        <td>".$row['phone']."</td>
                        <td>".$row['role']."</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No users found</td></tr>";
        }
        ?>
    </table>
    <br><a href="index.php">Go back</a>
</body>
</html>
