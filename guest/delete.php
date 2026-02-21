<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM tbGuest WHERE GuestID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("location:list.php");
