<?php
include "../config/db.php";

$id = (int)($_GET['id'] ?? 0);

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM tbBeverage WHERE BeverageID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$beverage = $result->fetch_assoc();
$stmt->close();

if(!$beverage){
    echo "Beverage not found!";
    exit;
}

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE tbBeverage SET Beverage = ?, StockQty = ?, SaleUnitPrice = ? WHERE BeverageID = ?");
    $stmt->bind_param("sidi", $name, $qty, $price, $id);

    if($stmt->execute()){
        echo "<script>alert('Updated successfully!'); window.location='list.php';</script>";
    } else {
        echo "<p style='color:red'>Error: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Beverage</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background: #0069d9; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>

<div class="container">
<h2>✏️ Edit Beverage</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>

<form method="post">
    <label>Beverage Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($beverage['Beverage']); ?>" required>
    
    <label>Stock Qty:</label>
    <input type="number" name="qty" value="<?php echo $beverage['StockQty']; ?>" required>

    <label>Sale Price ($):</label>
    <input type="number" name="price" step="0.01" min="0" value="<?php echo $beverage['SaleUnitPrice']; ?>" required>

    <button type="submit" name="update">Update Beverage</button>
</form>
</div>

</body>
</html>