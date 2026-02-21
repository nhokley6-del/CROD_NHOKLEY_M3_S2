<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$id = (int)($_GET['id'] ?? 0);

// 1. Check if Drink exists
$stmt = $conn->prepare("SELECT DrinkID FROM tbDrink WHERE DrinkID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
if($stmt->get_result()->num_rows == 0){
    echo "Drink not found!";
    exit;
}
$stmt->close();

// 2. Check if Drink has been sold (Foreign Key Check)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbDrinkOrder WHERE DrinkID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$count = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

if($count > 0){
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Error</title>";
    echo "<style>
            body { font-family: 'Segoe UI', sans-serif; background: pink; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .error-box { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }
            h3 { color: #dc3545; margin-top: 0; }
            a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; }
            a:hover { background: #5a6268; }
          </style></head><body>";
    echo "<div class='error-box'>";
    echo "<h3>❌ Error: Cannot delete this drink because it has been sold $count times!</h3>";
    echo "<a href='list.php'>Back to List</a>";
    echo "</div></body></html>";
    exit;
}

// 3. Delete
$stmt = $conn->prepare("DELETE FROM tbDrink WHERE DrinkID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
?>