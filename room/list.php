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
    <title>Room List</title>
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

<h2>Room List</h2>
<br><br>
<a href="../index.php" class="btn btn-back">← Back</a>
<a href="create.php" class="btn"> + Add New Room</a>

<table>
    <thead>
        <tr>
            <th>Room No</th>
            <th>Room Type</th>
            <th>Status</th>
            <th>Occupied By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT
                    r.RoomNo,
                    r.Status,
                    rt.RoomType,
                    (
                        SELECT g.GuestName
                        FROM tbGuest g
                        JOIN tbCheckIn ci ON g.GuestID = ci.GuestID
                        JOIN tbCheckInDetail cid ON ci.CheckInID = cid.CheckInID
                        WHERE cid.RoomNo = r.RoomNo AND ci.CheckInID NOT IN (SELECT CheckInID FROM tbCheckout)
                        LIMIT 1
                    ) AS GuestName
                FROM
                    tbRoom r
                LEFT JOIN
                    tbRoomType rt ON r.RoomTypeID = rt.RoomTypeID
                ORDER BY
                    r.RoomNo ASC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $status = htmlspecialchars($row['Status']);
                $status_color = 'black';
                if ($status == 'Available') {
                    $status_color = 'green';
                } elseif ($status == 'Occupied') {
                    $status_color = 'red';
                } elseif ($status == 'Maintenance') {
                    $status_color = 'orange';
                }

                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['RoomNo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['RoomType'] ?? 'N/A') . "</td>";
                echo "<td><strong style='color:$status_color;'>" . $status . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['GuestName'] ?? '---') . "</td>";
                echo "<td>
                        <a href='edit.php?id=" . $row['RoomNo'] . "' class='btn btn-edit'>Edit</a>
                        <a href='delete.php?id=" . $row['RoomNo'] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center'>No rooms found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>