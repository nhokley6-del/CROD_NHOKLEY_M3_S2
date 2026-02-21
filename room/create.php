<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$types=mysqli_query($conn,"SELECT * FROM tbRoomType");

if(isset($_POST['save'])){
    $error_message = null;
    $roomNo = $_POST['room'];
    $floor = $_POST['floor'];
    $roomTypeID = $_POST['type'];
    $status = 'Available';
    
    // Check if room number already exists
    $check_stmt = $conn->prepare("SELECT RoomNo FROM tbRoom WHERE RoomNo = ?");
    $check_stmt->bind_param("s", $roomNo);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error_message = "Error: Room number '" . htmlspecialchars($roomNo) . "' already exists.";
    } else {
        // If not, proceed with insertion
        $stmt = $conn->prepare("INSERT INTO tbRoom(RoomNo, Floor, Status, RoomTypeID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $roomNo, $floor, $status, $roomTypeID);
        if($stmt->execute()){
            header("Location: list.php");
            exit();
        } else {
            $error_message = "An unexpected error occurred: " . $stmt->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
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
<h2>Add New Room</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<a href="list.php" class="btn btn-list-view">List View</a>

<?php if(!empty($error_message)): ?>
    <div style="color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 15px 0; border: 1px solid #f5c6cb;"><?= $error_message ?></div>
<?php endif; ?>

<form method="post">
<label>Room No</label> <input name="room" required>
<label>Floor</label> <input name="floor" required>
<label>Room Type</label>
<select name="type" required>
<?php while($t=mysqli_fetch_assoc($types)){ ?>
<option value="<?=$t['RoomTypeID']?>"><?=$t['RoomType']?></option>
<?php } ?>
</select>
<button name="save">Save Room</button>
</form>
</div>
</body>
</html>
