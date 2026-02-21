<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$guests = mysqli_query($conn,"SELECT * FROM tbGuest");
$staff  = mysqli_query($conn,"SELECT * FROM tbStaff");
// Join with RoomType to get more details for the user
$rooms  = mysqli_query($conn,"SELECT r.RoomNo, r.Status, rt.RoomType, rt.UnitPrice FROM tbRoom r JOIN tbRoomType rt ON r.RoomTypeID = rt.RoomTypeID ORDER BY r.RoomNo");

if(isset($_POST['save'])){
    $conn->begin_transaction();
    try {
        $guest = $_POST['guest'];
        $staffID = $_POST['staff'];
        $dateIn  = $_POST['datein'];
        $dateOut = $_POST['dateout'];
        $prepaid = $_POST['prepaid'];

        // 1. Insert into tbBooking using prepared statements
        $stmt1 = $conn->prepare("INSERT INTO tbBooking(CheckInDate, CheckOutDate, GuestID, StaffID, TotalPrepaid) VALUES (?, ?, ?, ?, ?)");
        $stmt1->bind_param("ssiid", $dateIn, $dateOut, $guest, $staffID, $prepaid);
        $stmt1->execute();
        $bookingID = $conn->insert_id;
        $stmt1->close();

        if (empty($_POST['room'])) {
            throw new Exception("Please select at least one room.");
        }

        // 2. Insert into tbBookingDetail using prepared statements
        $stmt2 = $conn->prepare("INSERT INTO tbBookingDetail(BookingID, RoomNo, StayingUnitPrice, Prepaid) VALUES (?, ?, ?, ?)");
        foreach($_POST['room'] as $roomValue){
            // The value is "RoomNo|UnitPrice", so we split it
            list($roomNo, $unitPrice) = explode('|', $roomValue);
            $stmt2->bind_param("isdd", $bookingID, $roomNo, $unitPrice, $prepaid);
            $stmt2->execute();
        }
        $stmt2->close();

        $conn->commit();
        header("location:list.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Booking failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Booking</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; font-weight: 500; }
        input[type="text"], input[type="date"], input[type="number"], select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; transition: background 0.3s; }
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
<h2>Room Booking</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>

<a href="list.php" class="btn btn-list-view">List View</a>
<?php if(isset($error_message)): ?>
    <div style="color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<form method="post">

<label>Guest:</label>
<select name="guest" required>
<option value="">-- Select Guest --</option>
<?php while($g=mysqli_fetch_assoc($guests)){ ?>
<option value="<?=$g['GuestID']?>"><?=$g['GuestName']?></option>
<?php } ?>
</select>

<label>Staff:</label>
<select name="staff" required>
<option value="">-- Select Staff --</option>
<?php while($s=mysqli_fetch_assoc($staff)){ ?>
<option value="<?=$s['StaffID']?>"><?=$s['StaffName']?></option>
<?php } ?>
</select>

<label>CheckIn Date</label> <input type="date" name="datein" required value="<?php echo date('Y-m-d'); ?>">
<label>CheckOut Date</label> <input type="date" name="dateout" required>

<label>Select Room:</label>
<select name="room[]" required>
<option value="">-- Select Room --</option>
<?php 
if(mysqli_num_rows($rooms) > 0) {
    while($r = mysqli_fetch_assoc($rooms)){ ?>
    while($r = mysqli_fetch_assoc($rooms)){ ?>    
    <option value="<?=$r['RoomNo']?>|<?=$r['UnitPrice']?>">Room <?=$r['RoomNo']?> (<?=htmlspecialchars($r['RoomType'])?> - $<?=number_format($r['UnitPrice'], 2)?>) [<?=$r['Status']?>]</option>
<?php }
} else {
    echo "<option value='' disabled>No available rooms</option>";
} ?>
</select>

<label>Prepaid ($)</label> <input name="prepaid" type="number" step="0.01" required>

<button name="save">SAVE BOOKING</button>
</form>
</div>
</body>
</html>
