<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$guests = mysqli_query($conn,"SELECT * FROM tbGuest");
// Join with RoomType to get price and type details
$rooms  = mysqli_query($conn,"SELECT r.RoomNo, rt.RoomType, rt.UnitPrice FROM tbRoom r JOIN tbRoomType rt ON r.RoomTypeID = rt.RoomTypeID WHERE r.Status='Available' ORDER BY r.RoomNo");
$rooms  = mysqli_query($conn,"SELECT r.RoomNo, r.Status, rt.RoomType, rt.UnitPrice FROM tbRoom r JOIN tbRoomType rt ON r.RoomTypeID = rt.RoomTypeID ORDER BY r.RoomNo");

if(isset($_POST['checkin'])){

    $guest  = $_POST['guest'];
    $dateIn = $_POST['datein'];
    $dateOut= $_POST['dateout'];
    $prepaid= $_POST['prepaid'];

    // 1. Save CheckIn using Prepared Statement
    $stmt = $conn->prepare("INSERT INTO tbCheckIn(CheckInDate,CheckOutDate,GuestID,TotalPrepaid) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssid", $dateIn, $dateOut, $guest, $prepaid);
    $stmt->execute();
    $checkinID = $conn->insert_id;
    $stmt->close();

    // 2. Save Details and Update Room Status
    $stmtDetail = $conn->prepare("INSERT INTO tbCheckInDetail(CheckInID,RoomNo,StayingUnitPrice,Prepaid) VALUES(?, ?, ?, ?)");
    $stmtUpdate = $conn->prepare("UPDATE tbRoom SET Status='Occupied' WHERE RoomNo=?");

    foreach($_POST['room'] as $roomValue){
        // Split value to get RoomNo and Price (e.g., "101|50.00")
        list($roomNo, $price) = explode('|', $roomValue);

        // Insert detail
        $stmtDetail->bind_param("isdd", $checkinID, $roomNo, $price, $prepaid);
        $stmtDetail->execute();

        // Update room status
        $stmtUpdate->bind_param("s", $roomNo);
        $stmtUpdate->execute();
    }
    
    $stmtDetail->close();
    $stmtUpdate->close();

    header("location:list.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check In</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; font-weight: 500; }
        input[type="text"], input[type="date"], input[type="number"], select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; transition: background 0.3s; }
        button:hover { background: #218838; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 10px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>

<div class="container">
<h2>Check In</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>

<form method="post">

<label>Guest :</label>
<select name="guest" required>
<option value="">-- Select Guest --</option>
<?php while($g=mysqli_fetch_assoc($guests)){ ?>
<option value="<?=$g['GuestID']?>"><?=$g['GuestName']?></option>
<?php } ?>
</select>

<label>CheckIn Date :</label> 
<input type="date" name="datein" required value="<?php echo date('Y-m-d'); ?>">

<label>CheckOut Date :</label> 
<input type="date" name="dateout" required>

<label>Select Room:</label>
<select name="room[]" required>
<option value="">-- Select Room --</option>
<?php 
if(mysqli_num_rows($rooms) > 0) {
    while($r = mysqli_fetch_assoc($rooms)){ ?>    
    <option value="<?=$r['RoomNo']?>|<?=$r['UnitPrice']?>">Room <?=$r['RoomNo']?> (<?=htmlspecialchars($r['RoomType'])?> - $<?=number_format($r['UnitPrice'], 2)?>) [<?=$r['Status']?>]</option>
<?php }
} else {
    echo "<option value='' disabled>No available rooms</option>";
} ?>
</select>

<label>Prepaid ($) :</label> 
<input name="prepaid" type="number" step="0.01" required>

<button name="checkin">CHECK IN</button>
</form>
</div>
</body>
</html>
