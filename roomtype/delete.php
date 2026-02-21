<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

include "../config/db.php";

$id = (int) ($_GET['id'] ?? 0);

// Check if room type exists
$stmt = $conn->prepare("SELECT RoomTypeID FROM tbRoomType WHERE RoomTypeID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$check = $stmt->get_result();
if($check->num_rows == 0){
    echo "<div style='font-family:sans-serif; padding:20px; color:red;'>Error: Room Type not found! <a href='list.php'>Back</a></div>";
    exit;
}
$stmt->close();

// Check if any rooms use this type
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbRoom WHERE RoomTypeID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$rooms = $stmt->get_result();
$rowCount = $rooms->fetch_assoc()['count'];
$stmt->close();

if($rowCount > 0){
    echo "<div style='font-family:sans-serif; padding:20px; color:red;'>Error: Cannot delete this room type because $rowCount room(s) are using it! <a href='list.php'>Back</a></div>";
    exit;
}

// Delete the room type
$stmt = $conn->prepare("DELETE FROM tbRoomType WHERE RoomTypeID = ?");
$stmt->bind_param("i", $id);
$result = $stmt->execute();

if($result){
    header("Location:list.php");
} else {
    echo "<div style='font-family:sans-serif; padding:20px; color:red;'>Error: " . $conn->error . "</div>";
}
?>
