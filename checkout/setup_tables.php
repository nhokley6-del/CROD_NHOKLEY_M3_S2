<?php
include "../config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Setup Checkout Module</title>
<style>
    body { font-family: 'Segoe UI', sans-serif; padding: 40px; background: pink; text-align: center; }
    .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: inline-block; text-align: left; }
</style>
</head>
<body>
<div class="box">
<h2>Checkout Module Setup</h2>
<?php

// 1. Check for tbCheckout table
$table_exists_res = $conn->query("SHOW TABLES LIKE 'tbCheckout'");
if ($table_exists_res->num_rows == 0) {
    // Create table if it doesn't exist
    $sql_create = "CREATE TABLE tbCheckout (
        CheckoutID INT AUTO_INCREMENT PRIMARY KEY,
        CheckInID INT NOT NULL,
        RoomID VARCHAR(50),
        TotalAmount DECIMAL(10,2) NOT NULL,
        CheckoutDate DATETIME NOT NULL
    );";
    if ($conn->query($sql_create) === TRUE) {
        echo "<p style='color:green'>✅ Table 'tbCheckout' created successfully.</p>";
    } else {
        echo "<p style='color:red'>❌ Error creating table 'tbCheckout': " . $conn->error . "</p>";
    }
} else {
    // If table exists, check for the CheckInID column
    $checkin_col_res = $conn->query("SHOW COLUMNS FROM tbCheckout LIKE 'CheckInID'");
    if ($checkin_col_res->num_rows == 0) {
        $sql_add_col = "ALTER TABLE tbCheckout ADD COLUMN CheckInID INT NOT NULL AFTER CheckoutID";
        if ($conn->query($sql_add_col) === TRUE) {
            echo "<p style='color:green'>🔧 Fixed: Added missing column 'CheckInID' to 'tbCheckout'.</p>";
        } else {
            echo "<p style='color:red'>❌ Error adding column 'CheckInID': " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:green'>✅ Table 'tbCheckout' is correctly configured.</p>";
    }
}

echo "<hr>";
echo "<h3>👉 <a href='../checkin/list.php'>Click here to return to the Check-In List</a></h3>";
?>
</div>
</body>
</html>