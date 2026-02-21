<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php";

$id = $_GET['id'] ?? '';

if ($id) {
    // Check for dependencies (Booking Detail)
    $check = $conn->prepare("SELECT COUNT(*) FROM tbBookingDetail WHERE RoomNo = ?");
    $check->bind_param("s", $id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>alert('Cannot delete Room $id because it has booking history.'); window.location='list.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM tbRoom WHERE RoomNo = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            header("Location: list.php");
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt->close();
    }
} else {
    header("Location: list.php");
}
?>