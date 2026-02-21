<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Hotel System</title>
<style>
body{font-family:Arial;background:#f5f5f5}
.menu a{padding:10px;background:#007bff;color:white;margin:5px;text-decoration:none}
table{background:white;width:100%;border-collapse:collapse}
th,td{border:1px solid #ccc;padding:8px}
</style>
</head>
<body>

<div class="menu">
<a href="/hotelsystem/index.php">Home</a>
<a href="/hotelsystem/guest/list.php">Guests</a>
<a href="/hotelsystem/booking/list.php">Booking</a>
<a href="/hotelsystem/checkin/list.php">Check-In</a>
<a href="/hotelsystem/checkout/list.php">Check-Out</a>
<a href="/hotelsystem/login/logout.php">Logout</a>
</div>
<hr>
