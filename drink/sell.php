<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

if(isset($_POST['sell'])){
    $checkinID = $_POST['checkinID'];
    $drinkID = $_POST['drinkID'];
    $qty = (int)$_POST['qty'];

    // Get Drink Price
    $stmt = $conn->prepare("SELECT Price FROM tbDrink WHERE DrinkID = ?");
    $stmt->bind_param("i", $drinkID);
    $stmt->execute();
    $res = $stmt->get_result();
    $drink = $res->fetch_assoc();
    $stmt->close();

    if($drink){
        $amount = $drink['Price'] * $qty;

        // Insert Order
        $stmt = $conn->prepare("INSERT INTO tbDrinkOrder(CheckInID, DrinkID, Qty, Amount, OrderDate) VALUES(?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiid", $checkinID, $drinkID, $qty, $amount);
        
        if($stmt->execute()){
            echo "<p style='color:green; font-weight:bold;'>✅ Drink sold successfully!</p>";
        } else {
            echo "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Drink</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; font-weight: 500; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; transition: background 0.3s; }
        button:hover { background: #218838; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 20px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; margin-right: 10px; }
        .btn-back:hover { background: #5a6268; }
        .btn-report { background: #17a2b8; }
        .btn-report:hover { background: #138496; }
    </style>
</head>
<body>

<div class="container">
<h2>🍹 Sell Drink</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<a href="report.php" class="btn btn-report">📊 View Report</a>

<form method="post">
    <label>Select Guest (Room):</label>
    <select name="checkinID" required>
        <option value="">-- Select Guest --</option>
        <?php
        // Fetch active checkins (assuming you want to sell to current guests)
        $guests = $conn->query("SELECT c.CheckInID, g.GuestName FROM tbCheckIn c JOIN tbGuest g ON c.GuestID = g.GuestID ORDER BY c.CheckInID DESC");
        while($row = $guests->fetch_assoc()){
            echo "<option value='".$row['CheckInID']."'>".$row['GuestName']."</option>";
        }
        ?>
    </select>

    <label>Select Drink:</label>
    <select name="drinkID" required>
        <option value="">-- Select Drink --</option>
        <?php
        $drinks = $conn->query("SELECT * FROM tbDrink");
        while($row = $drinks->fetch_assoc()){
            echo "<option value='".$row['DrinkID']."'>".$row['DrinkName']." ($".$row['Price'].")</option>";
        }
        ?>
    </select>

    <label>Quantity:</label>
    <input type="number" name="qty" value="1" min="1" required>

    <button type="submit" name="sell">Confirm Sale</button>
</form>
</div>

</body>
</html>