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
    <title>Drink Sales Report</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: #fff; text-transform: uppercase; font-size: 0.9em; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #f1f1f1; }
        .btn { text-decoration: none; padding: 8px 15px; background: #28a745; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; }
        .btn:hover { background: #218838; }
        .btn-back { background: #6c757d; margin-right: 10px; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>

<h2>📊 Drink Sales Report</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<a href="sell.php" class="btn"> + Sell New Drink</a>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Guest Name</th>
            <th>Drink</th>
            <th>Qty</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT o.OrderID, o.OrderDate, o.Qty, o.Amount, 
                       d.DrinkName, g.GuestName 
                FROM tbDrinkOrder o 
                JOIN tbDrink d ON o.DrinkID = d.DrinkID
                JOIN tbCheckIn c ON o.CheckInID = c.CheckInID
                JOIN tbGuest g ON c.GuestID = g.GuestID
                ORDER BY o.OrderDate DESC";
        
        $result = $conn->query($sql);
        $grandTotal = 0;

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $grandTotal += $row['Amount'];
                echo "<tr>";
                echo "<td>" . $row['OrderID'] . "</td>";
                echo "<td>" . $row['OrderDate'] . "</td>";
                echo "<td>" . htmlspecialchars($row['GuestName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['DrinkName']) . "</td>";
                echo "<td>" . $row['Qty'] . "</td>";
                echo "<td>$" . number_format($row['Amount'], 2) . "</td>";
                echo "</tr>";
            }
            echo "<tr style='background:#e2e3e5; font-weight:bold;'><td colspan='5' style='text-align:right'>Grand Total:</td><td>$" . number_format($grandTotal, 2) . "</td></tr>";
        } else {
            echo "<tr><td colspan='6' style='text-align:center'>No sales records found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>