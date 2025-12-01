<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
</head>
<body>
    <h2>User Records</h2>

    <?php
    include '../db_connect.php'; // Corrected path

    // 1. Prepare SQL SELECT statement
    $sql = "SELECT User_id, name, phone, role FROM User";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 2. Output data in a table format
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Name</th><th>Phone</th><th>Role</th></tr>';
        
        while($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row["User_id"] . '</td>';
            echo '<td>' . $row["name"] . '</td>';
            echo '<td>' . $row["phone"] . '</td>';
            echo '<td>' . $row["role"] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "0 results found.";
    }
    
    $conn->close();
    ?>
</body>
</html>