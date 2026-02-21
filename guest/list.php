<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}
include "../config/db.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest List</title>
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

<h2>Guest List</h2>
<br><br>
<a href="../index.php" class="btn btn-back">← Back</a>
<a href="create.php" class="btn"> + Add New Guest</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Sex</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $q = mysqli_query($conn, "SELECT * FROM tbGuest ORDER BY GuestID DESC");
    if(mysqli_num_rows($q) > 0) {
        while ($r = mysqli_fetch_assoc($q)) {
    ?>
            <tr>
                <td><?= $r['GuestID'] ?></td>
                <td><?= htmlspecialchars($r['GuestName']) ?></td>
                <td><?= htmlspecialchars($r['Sex']) ?></td>
                <td><?= htmlspecialchars($r['Phone']) ?></td>
                <td><?= htmlspecialchars($r['GuestAddress']) ?></td>
                <td>
                <a href="edit.php?id=<?= $r['GuestID'] ?>" class="btn btn-edit">Edit</a>
                <a href="delete.php?id=<?= $r['GuestID'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
    <?php 
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;'>No guests found. Click '+ Add New Guest' to begin.</td></tr>";
    }
    ?>
    </tbody>
</table>

</body>
</html>
