// users/update_user.php

<?php
include '../db_connect.php';

$user_id = $name = $phone = $role = '';
$message = '';

// --- STEP 1: Fetch User Data if ID is provided ---
if (isset($_POST['fetch_user'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    
    $sql = "SELECT User_id, name, phone, role FROM User WHERE User_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // 'i' for integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $phone = $row['phone'];
        $role = $row['role'];
    } else {
        $message = "<p style='color:red;'>User ID not found.</p>";
    }
    $stmt->close();
}

// --- STEP 2: Process the Update ---
if (isset($_POST['update_user'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $role = $conn->real_escape_string($_POST['role']);

    if (empty($user_id) || empty($name)) {
        $message = "<p style='color:red;'>User ID and Name are required!</p>";
    } else {
        $sql = "UPDATE User SET name = ?, phone = ?, role = ? WHERE User_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $phone, $role, $user_id); // 'sssi' for string, string, string, integer

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>User ID $user_id updated successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error updating record: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User Record</title>
</head>
<body>
    <h2>Update User Record</h2>
    <?php echo $message; ?>

    <form method="POST" action="update_user.php">
        <label for="user_id_fetch">Enter User ID to Update:</label>
        <input type="number" id="user_id_fetch" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" required>
        <input type="submit" name="fetch_user" value="Load User Data">
    </form>
    
    <hr>
    
    <?php if (!empty($user_id) && $message != "<p style='color:red;'>User ID not found.</p>") : ?>
    <h3>Editing User ID: <?php echo htmlspecialchars($user_id); ?></h3>
    <form method="POST" action="update_user.php">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>"> 

        <label for="name">New Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

        <label for="phone">New Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>"><br><br>
        
        <label for="role">New Role:</label>
        <select id="role" name="role">
            <?php 
            $roles = ['Customer', 'Server', 'Host', 'Manager'];
            foreach ($roles as $r) {
                $selected = ($r == $role) ? 'selected' : '';
                echo "<option value='$r' $selected>$r</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" name="update_user" value="Apply Update">
    </form>
    <?php endif; ?>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>

<?php $conn->close(); ?>