<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In List</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; display: inline-block; }        
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #28a745; color: #fff; text-transform: uppercase; font-size: 0.9em; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #f1f1f1; }
        .btn { text-decoration: none; padding: 6px 12px; background: #28a745; color: white; border-radius: 4px; font-size: 0.9em; margin-right: 5px; transition: opacity 0.3s; }
        .btn:hover { opacity: 0.9; }
        .btn-checkout { background: #dc3545; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>

<h2>Check-In List</h2>
<br><br>
<a href="../index.php" class="btn btn-back">← Back</a>
<a href="create.php" class="btn"> + New Check-In</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Guest</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Prepaid</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // --- Check for tbCheckout.CheckInID column to prevent fatal error ---
    $checkout_col_exists = false;
    $checkout_table_exists_res = $conn->query("SHOW TABLES LIKE 'tbCheckout'");
    if ($checkout_table_exists_res->num_rows > 0) {
        $checkout_col_res = $conn->query("SHOW COLUMNS FROM tbCheckout LIKE 'CheckInID'");
        if ($checkout_col_res->num_rows > 0) {
            $checkout_col_exists = true;
        }
    }

    if (!$checkout_col_exists) {
        echo "<tr><td colspan='7' style='text-align:center; background: #fff8e1;'><h4 style='color:#c0392b'>Database Error: `tbCheckout` table is missing or misconfigured.</h4><p>Please run the setup script to fix this.</p><p><a href='../checkout/setup_tables.php' style='padding:8px 12px; background:#007bff; color:white; text-decoration:none; border-radius:4px;'>Run Checkout Setup</a></p></td></tr>";
    } else {

    // Show all check-ins (Active and Checked Out)
    $q = mysqli_query($conn, "SELECT c.*, g.GuestName, checkout.CheckoutID 
        FROM tbCheckIn c 
        JOIN tbGuest g ON c.GuestID = g.GuestID 
        LEFT JOIN tbCheckout AS checkout ON c.CheckInID = checkout.CheckInID
        ORDER BY c.CheckInID DESC");

    if(mysqli_num_rows($q) > 0) {
        while ($r = mysqli_fetch_assoc($q)) {
            $status = $r['CheckoutID'] ? '<span style="color:red">Checked Out</span>' : '<span style="color:green">Active</span>';
    ?>
        <tr>
            <td><?= $r['CheckInID'] ?></td>
            <td><?= htmlspecialchars($r['GuestName']) ?></td>
            <td><?= date("d M, Y", strtotime($r['CheckInDate'])) ?></td>
            <td><?= date("d M, Y", strtotime($r['CheckOutDate'])) ?></td>
            <td>$<?= number_format($r['TotalPrepaid'], 2) ?></td>
            <td><?= $status ?></td>
            <td>
                <?php if(!$r['CheckoutID']): // Only show checkout button if not already checked out ?>
                <a href="../checkout/create.php?id=<?= $r['CheckInID'] ?>" class="btn btn-checkout">Check-Out</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php 
        }
    } else {
        echo "<tr><td colspan='7' style='text-align:center; background: #fff;'>No check-in records found.</td></tr>";
    }
    } // ends the 'else' for the column check
    ?>
    </tbody>
</table>

</body>
</html>
