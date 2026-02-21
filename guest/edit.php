<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header("location:list.php");
    exit();
}

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE tbGuest SET GuestName=?, Sex=?, Phone=?, GuestAddress=? WHERE GuestID=?");
    $stmt->bind_param("ssssi", $name, $sex, $phone, $address, $id);
    if ($stmt->execute()) {
        header("location:list.php");
        exit();
    } else {
        $error = "Update failed: " . $stmt->error;
    }
}

// Fetch existing data securely
$stmt = $conn->prepare("SELECT * FROM tbGuest WHERE GuestID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    echo "Guest not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Guest</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background: #0056b3; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="container">
<h2>Edit Guest</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<form method="post">
<label>Name</label> <input name="name" value="<?=htmlspecialchars($data['GuestName'])?>" required>
<label>Sex</label> <input name="sex" value="<?=htmlspecialchars($data['Sex'])?>" required>
<label>Phone</label> <input name="phone" value="<?=htmlspecialchars($data['Phone'])?>" required>
<label>Address</label> <textarea name="address" required><?=htmlspecialchars($data['GuestAddress'])?></textarea>
<button name="update">Update</button>
</form>
</div>
</body>
</html>
