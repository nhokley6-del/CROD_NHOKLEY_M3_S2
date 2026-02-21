<?php
include "../config/db.php";
$id=$_GET['id'];

$booking=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tbBooking WHERE BookingID=$id"));
$rooms=mysqli_query($conn,"SELECT * FROM tbBookingDetail WHERE BookingID=$id");

mysqli_query($conn,"INSERT INTO tbCheckIn(CheckInDate,CheckOutDate,GuestID,TotalPrepaid)
VALUES('{$booking['CheckInDate']}','{$booking['CheckOutDate']}','{$booking['GuestID']}','{$booking['TotalPrepaid']}')");

$checkinID=mysqli_insert_id($conn);

while($r=mysqli_fetch_assoc($rooms)){
    mysqli_query($conn,"INSERT INTO tbCheckInDetail
    VALUES('$checkinID','{$r['RoomNo']}','{$r['StayingUnitPrice']}','{$r['Prepaid']}')");

    mysqli_query($conn,"UPDATE tbRoom SET Status='Occupied' WHERE RoomNo='{$r['RoomNo']}'");
}

echo "Booking converted to Check-In!";
