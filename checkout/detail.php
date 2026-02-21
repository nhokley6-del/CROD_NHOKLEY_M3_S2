<?php
include "../config/db.php";

$id = (int)($_GET['id'] ?? 0);

// 1. Get Checkout Details
$stmt = $conn->prepare("SELECT * FROM tbCheckout WHERE CheckoutID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$checkout = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$checkout){
    echo "<h3 style='color:red; text-align:center; margin-top:50px;'>❌ Checkout record not found!</h3>";
    exit;
}

$checkinID = $checkout['CheckInID'] ?? 0;

// 2. Get Guest & Room Details
$sql_guest = "SELECT g.GuestName, r.RoomName, c.CheckInDate, c.CheckOutDate, rt.UnitPrice, rt.RoomType 
              FROM tbCheckIn c 
              JOIN tbGuest g ON c.GuestID = g.GuestID 
              JOIN tbRoom r ON c.RoomID = r.RoomID 
              JOIN tbRoomType rt ON r.RoomTypeID = rt.RoomTypeID 
              WHERE c.CheckInID = ?";

$stmt = $conn->prepare($sql_guest);
$stmt->bind_param("i", $checkinID);
$stmt->execute();
$guestInfo = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Calculate Days and Room Cost
$days = 0;
$roomPrice = 0;
$roomTotal = 0;

if($guestInfo){
    $in = new DateTime($guestInfo['CheckInDate']);
    $out = new DateTime($checkout['CheckoutDate']); // Use actual checkout date
    
    $interval = $in->diff($out);
    $days = $interval->days;
    if($days == 0) $days = 1; // Minimum 1 day charge
    
    $roomPrice = $guestInfo['UnitPrice'];
    $roomTotal = $days * $roomPrice;
}

// 3. Get Drink Orders
$sql_drinks = "SELECT d.DrinkName, o.Qty, o.Amount, d.Price 
               FROM tbDrinkOrder o 
               JOIN tbDrink d ON o.DrinkID = d.DrinkID 
               WHERE o.CheckInID = ?";
$stmt = $conn->prepare($sql_drinks);
$stmt->bind_param("i", $checkinID);
$stmt->execute();
$drinks = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $id; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background: pink; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { text-align: center; margin-bottom: 20px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f8f9fa; }
        .total-row { font-weight: bold; background-color: #f8f9fa; }
        .btn { text-decoration: none; padding: 10px 20px; background: #007bff; color: white; border-radius: 4px; display: inline-block; margin-top: 20px; cursor: pointer; border: none; }
        .btn-print { background: #28a745; }
        .btn-back { background: #6c757d; margin-left: 10px; }
        .btn-back:hover { background: #5a6268; }
        @media print { .btn { display: none; } body { background: white; } .invoice-box { box-shadow: none; border: none; } }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        <h1>Hotel Invoice</h1>
        <p>Invoice #: <?php echo $id; ?></p>
        <p>Date: <?php echo $checkout['CheckoutDate']; ?></p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Guest Name:</strong> <?php echo htmlspecialchars($guestInfo['GuestName'] ?? 'N/A'); ?></td>
            <td><strong>Room:</strong> <?php echo htmlspecialchars($guestInfo['RoomName'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($guestInfo['RoomType'] ?? ''); ?>)</td>
        </tr>
        <tr>
            <td><strong>Check In:</strong> <?php echo $guestInfo['CheckInDate'] ?? '-'; ?></td>
            <td><strong>Check Out:</strong> <?php echo $checkout['CheckoutDate']; ?></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr><th>Description</th><th>Price</th><th>Qty / Days</th><th>Amount</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Room Charge (<?php echo htmlspecialchars($guestInfo['RoomType'] ?? 'Standard'); ?>)</td>
                <td>$<?php echo number_format($roomPrice, 2); ?></td>
                <td><?php echo $days; ?> Day(s)</td>
                <td>$<?php echo number_format($roomTotal, 2); ?></td>
            </tr>
            <?php if($drinks && $drinks->num_rows > 0): while($d = $drinks->fetch_assoc()): ?>
            <tr>
                <td>Drink: <?php echo htmlspecialchars($d['DrinkName']); ?></td>
                <td>$<?php echo number_format($d['Price'], 2); ?></td>
                <td><?php echo $d['Qty']; ?></td>
                <td>$<?php echo number_format($d['Amount'], 2); ?></td>
            </tr>
            <?php endwhile; endif; ?>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Grand Total</td>
                <td>$<?php echo number_format($checkout['TotalAmount'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: center;">
        <button onclick="window.print()" class="btn btn-print">🖨 Print Invoice</button>
        <a href="list.php" class="btn btn-back">← Back to List</a>
    </div>
</div>

</body>
</html>