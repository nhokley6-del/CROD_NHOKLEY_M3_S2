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
    <title>Staff List</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #28a745; color: #fff; text-transform: uppercase; font-size: 0.9em; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #f1f1f1; }
        img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
        .btn { text-decoration: none; padding: 6px 12px; background: #28a745; color: white; border-radius: 4px; font-size: 0.9em; margin-right: 5px; transition: opacity 0.3s; }
        .btn:hover { opacity: 0.9; }
        .btn-edit { background: #007bff; }
        .btn-delete { background: #dc3545; }
        .btn-back { background: #6c757d; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>

<h2>Staff List</h2>
<br><br>
<a href="../index.php" class="btn btn-back">← Back</a>
<a href="create.php" class="btn"> + Add New Staff</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Position</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM tbStaff";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $photo = !empty($row['Photo']) ? $row['Photo'] : 'default.png';
                echo "<tr>";
                echo "<td>" . $row['StaffID'] . "</td>";
                echo "<td><img src='../images/" . htmlspecialchars($photo) . "' alt='Staff Photo'></td>";
                echo "<td>" . htmlspecialchars($row['StaffName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['StaffPosition']) . "</td>";
                echo "<td>
                        <a href='edit.php?id=" . $row['StaffID'] . "' class='btn btn-edit'>Edit</a>
                        <a href='delete.php?id=" . $row['StaffID'] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this staff?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center'>No staff found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>