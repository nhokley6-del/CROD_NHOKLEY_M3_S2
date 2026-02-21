<?php
include "../config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beverage List</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #28a745; color: #fff; text-transform: uppercase; font-size: 0.9em; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #f1f1f1; }
        .btn { text-decoration: none; padding: 6px 12px; background: #28a745; color: white; border-radius: 4px; font-size: 0.9em; margin-right: 5px; transition: opacity 0.3s; }
        .btn:hover { opacity: 0.9; }
        .btn-delete { background: #dc3545; }
        .btn-edit { background: #007bff; }
    </style>
</head>
<body>

<h2>Beverage List</h2>
<br><br>
<a href="create.php" class="btn"> + Add New Beverage</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Beverage Name</th>
            <th>Stock Qty</th>
            <th>Sale Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM tbBeverage";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . ($row['BeverageID'] ?? $row['id'] ?? '-') . "</td>"; 
                echo "<td>" . htmlspecialchars($row['Beverage']) . "</td>";
                echo "<td>" . htmlspecialchars($row['StockQty']) . "</td>";
                echo "<td>$" . number_format($row['SaleUnitPrice'], 2) . "</td>";
                echo "<td>
                        <a href='edit.php?id=" . ($row['BeverageID'] ?? $row['id'] ?? 0) . "' class='btn btn-edit'>Edit</a>
                        <a href='delete.php?id=" . ($row['BeverageID'] ?? $row['id'] ?? 0) . "' class='btn btn-delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center'>No beverages found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>