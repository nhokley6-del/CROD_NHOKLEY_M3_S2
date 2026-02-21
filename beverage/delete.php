<?php
include "../config/db.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM tbBeverage WHERE BeverageID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
?>