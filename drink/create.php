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

if(isset($_POST['save'])){
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Use prepared statement to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO tbDrink(DrinkName, Price) VALUES(?, ?)");
    $stmt->bind_param("sd", $name, $price);

    if($stmt->execute()){
        echo "<p style='color:green; font-weight:bold;'>✅ Drink added successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drink</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background: #218838; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; margin-right: 10px; }
        .btn-back:hover { background: #5a6268; }
        .btn-report { background: #17a2b8; }
        .btn-report:hover { background: #138496; }
    </style>
</head>
<body>

<div class="container">
<h2>🍺 Add New Drink</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<a href="report.php" class="btn btn-report">📊 View Report</a>

<form method="post">
    <label>Drink Name:</label>
    <input type="text" name="name" required placeholder="e.g. Coca Cola">
    
    <label>Price ($):</label>
    <input type="number" name="price" step="0.01" min="0" required placeholder="0.00">

    <button type="submit" name="save">Save Drink</button>
</form>
</div>

</body>
</html>
