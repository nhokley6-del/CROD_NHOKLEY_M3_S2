<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

include "../config/db.php";

if (!isset($_GET['id'])) {
    echo "Check-In ID is missing.";
    exit;
}

$checkinID = (int)$_GET['id'];

// 1. Get Check-In, Guest, and Room Info (Securely)
$stmt = $conn->prepare("
    SELECT c.CheckInID, c.CheckInDate, c.TotalPrepaid, g.GuestName, cd.RoomNo, rt.UnitPrice
    FROM tbCheckIn c
    JOIN tbGuest g ON c.GuestID = g.GuestID
    JOIN tbCheckInDetail cd ON c.CheckInID = cd.CheckInID
    JOIN tbRoom r ON cd.RoomNo = r.RoomNo
    JOIN tbRoomType rt ON r.RoomTypeID = rt.RoomTypeID
    WHERE c.CheckInID = ?");
$stmt->bind_param("i", $checkinID);
$stmt->execute();
$checkin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$checkin) {
    echo "Check-In record not found.";
    exit;
}

// 2. Calculate Room Cost
$checkInDate = new DateTime($checkin['CheckInDate']);
$checkOutDate = new DateTime(); // Today's date for checkout
$days = $checkInDate->diff($checkOutDate)->days;
if ($days == 0) $days = 1; // Minimum 1 day charge
$roomTotal = $days * $checkin['UnitPrice'];

// 3. Get Total Drink Cost
$stmt = $conn->prepare("SELECT SUM(Amount) as TotalDrinks FROM tbDrinkOrder WHERE CheckInID = ?");
$stmt->bind_param("i", $checkinID);
$stmt->execute();
$drinksTotal = $stmt->get_result()->fetch_assoc()['TotalDrinks'] ?? 0;
$stmt->close();

// 4. Calculate Grand Total
$grandTotal = ($roomTotal + $drinksTotal) - $checkin['TotalPrepaid'];


// 5. Process Checkout on Form Submission
if (isset($_POST['checkout'])) {
    $conn->begin_transaction();
    try {
        // Insert into checkout table
        $stmt1 = $conn->prepare("INSERT INTO tbCheckout(CheckInID, RoomID, TotalAmount, CheckoutDate) VALUES (?, ?, ?, NOW())");
        $stmt1->bind_param("isd", $checkinID, $checkin['RoomNo'], $grandTotal);
        $stmt1->execute();
        $newCheckoutID = $conn->insert_id;
        $stmt1->close();

        // Update room status to 'Available'
        $stmt2 = $conn->prepare("UPDATE tbRoom SET Status = 'Available' WHERE RoomNo = ?");
        $stmt2->bind_param("s", $checkin['RoomNo']);
        $stmt2->execute();
        $stmt2->close();

        // (Optional but good practice) Update checkin record
        $stmt3 = $conn->prepare("UPDATE tbCheckIn SET CheckOutDate = NOW() WHERE CheckInID = ?");
        $stmt3->bind_param("i", $checkinID);
        $stmt3->execute();
        $stmt3->close();

        $conn->commit();
        // Redirect to the new invoice detail page
        header("Location: detail.php?id=" . $newCheckoutID);
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        echo "Checkout Failed: " . $exception->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Bill</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h2 { color: #333; border-bottom: 2px solid #dc3545; padding-bottom: 10px; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 10px 0; border-bottom: 1px solid #eee; color: #555; }
        strong { color: #000; }
        button { width: 100%; padding: 12px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1.1em; margin-top: 20px; transition: background 0.3s; }
        button:hover { background: #c82333; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; margin-right: 10px; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="container">
<h2>CHECK OUT BILL</h2>
<br><br>
<a href="../checkin/list.php" class="btn btn-back">← Back to List</a>

<table style="line-height: 1.8;">
    <tr>
        <td style="width: 150px;">Guest Name</td>
        <td>: <strong><?php echo htmlspecialchars($checkin['GuestName']); ?></strong></td>
    </tr>
    <tr>
        <td>Days Stayed</td>
        <td>: <?php echo $days; ?> day(s)</td>
    </tr>
    <tr>
        <td>Room Total</td>
        <td>: $<?php echo number_format($roomTotal, 2); ?></td>
    </tr>
    <tr>
        <td>Drinks & Services</td>
        <td>: $<?php echo number_format($drinksTotal, 2); ?></td>
    </tr>
    <tr>
        <td>Prepaid Deposit</td>
        <td>: -$<?php echo number_format($checkin['TotalPrepaid'], 2); ?></td>
    </tr>
    <tr style="font-weight: bold; font-size: 1.2em; border-top: 1px solid #ccc;">
        <td>Grand Total</td>
        <td>: $<?php echo number_format($grandTotal, 2); ?></td>
    </tr>
</table>

<form method="post" style="margin-top: 20px;">
    <button name="checkout">CONFIRM & COMPLETE CHECKOUT</button>
</form>
</div>
</body>
</html>
