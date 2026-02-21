<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; margin: 0; padding: 40px; }
        .container { max-width: 1000px; margin: 0 auto; text-align: center; }
        h1 { color: #2c3e50; margin-bottom: 40px; font-size: 2.5em; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card { background: #fff; padding: 30px 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-decoration: none; color: #555; transition: all 0.3s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; border-bottom: 4px solid transparent; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); color: #007bff; border-bottom-color: #007bff; }
        .icon { font-size: 3em; margin-bottom: 15px; }
        .label { font-size: 1.2em; font-weight: 600; }
        .logout-btn { position: absolute; top: 30px; right: 30px; padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        .logout-btn:hover { background-color: #c82333; }
    </style>
</head>
<body>

<a href="logout.php" class="logout-btn">Logout</a>


<div class="container">
    <h1>Hotel Management System</h1>
    <p style="margin-top:-30px; margin-bottom: 30px; color: #555;">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>!</p>

    <div class="dashboard-grid">
        <a href="guest/list.php" class="card">
            <span class="icon"><i class="fa-solid fa-person-circle-question"></i></span>
            <span class="label">Guest</span>
        </a>
        <a href="staff/list.php" class="card">
            <span class="icon"><i class="fa-solid fa-clipboard-user"></i></span>
            <span class="label">Staff</span>
        </a>
        <a href="room/list.php" class="card">
            <span class="icon"><i class="fa-solid fa-hotel"></i></span>
            <span class="label">Room</span>
        </a>
        <a href="drink/list.php" class="card">
            <span class="icon">🥤</span>
            <span class="label">Beverage</span>
        </a>
        <a href="booking/list.php" class="card">
            <span class="icon">📅</span>
            <span class="label">Booking</span>
        </a>
        <a href="checkin/list.php" class="card">
            <span class="icon">
                <i class="fa-solid fa-key"></i>
            </span>
            <span class="label">CheckIn</span>
        </a>
        <a href="drink/sell.php" class="card">
            <span class="icon"><i class="fa-solid fa-wine-bottle"></i></span>
            <span class="label">Drink Sale</span>
        </a>
        <a href="drink/report.php" class="card">
            <span class="icon">📊</span>
            <span class="label">Drink Report</span>
        </a>
    </div>

</div>

</body>
</html>
