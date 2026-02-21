<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$id = $_GET['id'] ?? '';
if ($id === '') {
    header("Location: list.php");
    exit;
}

// Fetch room data
$stmt = $conn->prepare("SELECT * FROM tbRoom WHERE RoomNo = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room) {
    echo "Room not found!";
    exit;
}

$types = mysqli_query($conn, "SELECT * FROM tbRoomType");

if (isset($_POST['update'])) {
    $floor = $_POST['floor'];
    $type = $_POST['type'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tbRoom SET Floor = ?, RoomTypeID = ?, Status = ? WHERE RoomNo = ?");
    $stmt->bind_param("siss", $floor, $type, $status, $id);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit;
    } else {
        $error = "Error updating room: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background: #0069d9; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="container">
<h2>Edit Room</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    <label>Room No (Cannot be changed)</label>
    <input value="<?= htmlspecialchars($room['RoomNo']) ?>" disabled style="background:#e9ecef;">
    
    <label>Floor</label>
    <input name="floor" value="<?= htmlspecialchars($room['Floor']) ?>" required>
    
    <label>Room Type</label>
    <select name="type" required>
        <?php while ($t = mysqli_fetch_assoc($types)) { ?>
            <option value="<?= $t['RoomTypeID'] ?>" <?= $t['RoomTypeID'] == $room['RoomTypeID'] ? 'selected' : '' ?>>
                <?= $t['RoomType'] ?>
            </option>
        <?php } ?>
    </select>

    <label>Status</label>
    <select name="status">
        <option value="Available" <?= $room['Status'] == 'Available' ? 'selected' : '' ?>>Available</option>
        <option value="Occupied" <?= $room['Status'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
        <option value="Maintenance" <?= $room['Status'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
    </select>

    <button name="update">Update Room</button>
</form>
</div>
</body>
</html>