<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$id = (int)($_GET['id'] ?? 0);

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM tbDrink WHERE DrinkID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$drink = $result->fetch_assoc();
$stmt->close();

if(!$drink){
    echo "Drink not found!";
    exit;
}

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE tbDrink SET DrinkName = ?, Price = ? WHERE DrinkID = ?");
    $stmt->bind_param("sdi", $name, $price, $id);

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
    <title>Edit Drink</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 0; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background: #0069d9; }
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
<h2>✏️ Edit Drink</h2>
<br><br>
<a href="list.php" class="btn btn-back">← Back to List</a>
<a href="report.php" class="btn btn-report">📊 View Report</a>

<form method="post">
    <label>Drink Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($drink['DrinkName']); ?>" required>
    
    <label>Price ($):</label>
    <input type="number" name="price" step="0.01" min="0" value="<?php echo $drink['Price']; ?>" required>

    <button type="submit" name="update">Update Drink</button>
</form>
</div>

</body>
</html>