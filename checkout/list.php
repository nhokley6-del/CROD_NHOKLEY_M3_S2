<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout History</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #28a745; color: #fff; text-transform: uppercase; font-size: 0.9em; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #f1f1f1; }
        .btn { text-decoration: none; padding: 6px 12px; background: #007bff; color: white; border-radius: 4px; font-size: 0.9em; transition: opacity 0.3s; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>

<h2>Checkout History</h2>
<br><br>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Guest Name</th>
            <th>Total Amount</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT co.CheckoutID, co.TotalAmount, co.CheckoutDate, g.GuestName
                FROM tbCheckout co
                JOIN tbCheckIn ci ON co.CheckInID = ci.CheckInID
                JOIN tbGuest g ON ci.GuestID = g.GuestID
                ORDER BY co.CheckoutID DESC";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['CheckoutID'] . "</td>";
                echo "<td>" . htmlspecialchars($row['GuestName']) . "</td>";
                echo "<td>$" . number_format($row['TotalAmount'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['CheckoutDate']) . "</td>";
                echo "<td>
                        <a href='detail.php?id=" . $row['CheckoutID'] . "' class='btn'>View Detail</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center'>No checkout records found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>