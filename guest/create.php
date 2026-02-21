<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

if(isset($_POST['save'])){
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO tbGuest(GuestName, Sex, Phone, GuestAddress) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $sex, $phone, $address);
    
    if ($stmt->execute()) {
        header("location:list.php");
        exit();
    } else {
        // You can add error handling here if you want
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Guest</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background: #218838; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; margin-right: 10px; }
        .btn-back:hover { background: #5a6268; }
        .btn-list-view { background: #007bff; }
        .btn-list-view:hover { background: #0069d9; }
    </style>
</head>
<body>
<div class="container">
<h2>Add Guest</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<a href="list.php" class="btn btn-list-view">List View</a>
<form method="post">
<label>Name</label> <input name="name" required>
<label>Sex</label> <input name="sex" required>
<label>Phone</label> <input name="phone" required>
<label>Address</label> <textarea name="address" required></textarea>
<button name="save">Save</button>
</form>
</div>
</body>
</html>
