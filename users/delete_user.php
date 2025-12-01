// users/delete_user.php

<?php
include '../db_connect.php';

$message = '';

if (isset($_POST['delete_user'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);

    if (empty($user_id)) {
        $message = "<p style='color:red;'>User ID is required!</p>";
    } else {
        // Prepare SQL DELETE statement
        $sql = "DELETE FROM User WHERE User_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id); // 'i' for integer

        if ($stmt->execute()) {
            // Check if any row was affected
            if ($stmt->affected_rows > 0) {
                $message = "<p style='color:green;'>User ID $user_id deleted successfully!</p>";
            } else {
                $message = "<p style='color:orange;'>User ID $user_id not found or already deleted.</p>";
            }
        } else {
            $message = "<p style='color:red;'>Error deleting record: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete User Record</title>
</head>
<body>
    <h2>Delete User Record</h2>
    <?php echo $message; ?>

    <form method="POST" action="delete_user.php">
        <label for="user_id">User ID to Delete:</label>
        <input type="number" id="user_id" name="user_id" required><br><br>

        <input type="submit" name="delete_user" value="Delete User">
    </form>
    <br><a href="../index.php">Back to Main Menu</a>
</body>
</html>

<?php $conn->close(); ?>