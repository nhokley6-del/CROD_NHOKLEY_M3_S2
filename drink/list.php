<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

// --- Check for tbDrink table and columns ---
$table_exists_res = $conn->query("SHOW TABLES LIKE 'tbDrink'");
if ($table_exists_res->num_rows == 0) {
    echo "<div style='font-family:sans-serif; padding:20px; background:#fff8e1; border:1px solid #ffecb3; margin:20px;'><h3>Database table 'tbDrink' is missing.</h3><p>Please run the setup script to create the necessary tables for the drink module.</p><p><a href='setup_tables.php' style='padding:10px 15px; background:#007bff; color:white; text-decoration:none; border-radius:4px;'>Run Setup Script</a></p></div>";
    exit;
}
$drink_name_exists_res = $conn->query("SHOW COLUMNS FROM tbDrink LIKE 'DrinkName'");
$price_exists_res = $conn->query("SHOW COLUMNS FROM tbDrink LIKE 'Price'");
if ($drink_name_exists_res->num_rows == 0 || $price_exists_res->num_rows == 0) {
    echo "<div style='font-family:sans-serif; padding:20px; background:#fff8e1; border:1px solid #ffecb3; margin:20px;'><h3>Database table 'tbDrink' has incorrect columns.</h3><p>It might be missing 'DrinkName' or 'Price'. Please run the setup script to fix the table structure.</p><p><a href='setup_tables.php' style='padding:10px 15px; background:#007bff; color:white; text-decoration:none; border-radius:4px;'>Run Setup Script</a></p></div>";
    exit;
}
// --- End Check ---

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drink List</title>
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
        .btn-edit { background: #007bff; }
        .btn-delete { background: #dc3545; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>

<h2>Drink List</h2>
<br><br>
<a href="../index.php" class="btn btn-back">← Back</a>
<a href="create.php" class="btn"> + Add New Drink</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Drink Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Select from tbDrink to get DrinkName and Price
        $sql = "SELECT * FROM tbDrink";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['DrinkID'] . "</td>";
                // These keys (DrinkName, Price) must match the columns in tbDrink
                echo "<td>" . htmlspecialchars($row['DrinkName']) . "</td>";
                echo "<td>$" . number_format($row['Price'], 2) . "</td>";
                echo "<td>
                        <a href='edit.php?id=" . $row['DrinkID'] . "' class='btn btn-edit'>Edit</a>
                        <a href='delete.php?id=" . $row['DrinkID'] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align:center'>No drinks found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>