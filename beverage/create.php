<?php include "../config/db.php";
if(isset($_POST['save'])){
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO tbBeverage(Beverage, StockQty, SaleUnitPrice) VALUES(?, ?, ?)");
    $stmt->bind_param("sid", $name, $qty, $price);
    
    if($stmt->execute()){
        header("Location: list.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Beverage</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background: #218838; }
        .btn { text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; font-size: 0.9em; transition: background 0.3s; display: inline-block; margin-bottom: 15px; }
        .btn:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="container">
<h2>Add Beverage</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<form method="post">
<label>Name</label> <input name="name" required>
<label>Qty</label> <input name="qty" type="number" required>
<label>Sale Price</label> <input name="price" type="number" step="0.01" required>
<button name="save">Save</button>
</form>
</div>
</body>
</html>
