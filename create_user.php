<?php
include "config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Setup User</title>
<style>
    body { font-family: 'Segoe UI', sans-serif; padding: 40px; background: pink; text-align: center; }
    .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: inline-block; text-align: left; }
</style>
</head>
<body>
<div class="box">
<h2>System Setup</h2>
<?php

$username = "admin";
$password = password_hash("123456", PASSWORD_DEFAULT); // Encrypt password
$title = "Administrator";
$staffID = 1;

// 1. Check if Staff exists (to avoid Foreign Key error)
$stmt1 = $conn->prepare("SELECT StaffID FROM tbStaff WHERE StaffID = ?");
$stmt1->bind_param("i", $staffID);
$stmt1->execute();
$checkStaff = $stmt1->get_result();
if($checkStaff->num_rows == 0){
    // Insert dummy staff if not exists
    $stmt2 = $conn->prepare("INSERT INTO tbStaff(StaffID, StaffName, Phone, StaffPosition, Photo) VALUES(?, 'Admin Staff', '000', 'Admin', 'default.png')");
    $stmt2->bind_param("i", $staffID);
    if($stmt2->execute()){
        echo "<p style='color:green'>✅ Staff created (ID: 1).</p>";
    } else {
        echo "<p style='color:red'>❌ Staff Error: ".$conn->error."</p>";
    }
    $stmt2->close();
}
$stmt1->close();

// 2. Check if User exists
$stmt3 = $conn->prepare("SELECT UserID FROM tbUser WHERE UserName = ?");
$stmt3->bind_param("s", $username);
$stmt3->execute();
$checkUser = $stmt3->get_result();

if($checkUser->num_rows > 0){
    // Delete existing user to ensure a clean reset
    $stmt4 = $conn->prepare("DELETE FROM tbUser WHERE UserName = ?");
    $stmt4->bind_param("s", $username);
    $stmt4->execute();
    $stmt4->close();
}

// Insert new user
$stmt5 = $conn->prepare("INSERT INTO tbUser(UserName, UserPassword, UserTitle, StaffID) VALUES (?, ?, ?, ?)");
$stmt5->bind_param("sssi", $username, $password, $title, $staffID);
if($stmt5->execute()) echo "<p style='color:green'>✅ User 'admin' has been reset. Password: '123456'.</p>";
else echo "<p style='color:red'>❌ Error creating User: " . $conn->error . "</p>";
$stmt5->close();

$stmt3->close();

echo "<hr>";
echo "<h3>👉 <a href='login/login.php'>ចុចទីនេះដើម្បី Login (Click here to Login)</a></h3>";
echo "Username: <b>admin</b><br>";
echo "Password: <b>123456</b>";
?>
</div>
</body>
</html>
