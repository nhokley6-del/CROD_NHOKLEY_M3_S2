<?php
session_start();
include("../config/db.php");

if(isset($_POST['login'])){

    // Diagnostic Check: Is the user table empty?
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbUser");
    $check_stmt->execute();
    $user_count = $check_stmt->get_result()->fetch_assoc()['count'];
    $check_stmt->close();

    if ($user_count == 0) {
        $error = "<b>No users found in the database.</b><br><br>Please run the setup script to create the default admin user.<br><br><a href='../create_user.php' style='color:#0056b3;'>Click here to run setup</a>";
    } else {

    $user = trim($_POST['user']); // Trim whitespace from username
    $pass = $_POST['pass']; // Passwords can have spaces, so don't trim

    $stmt = $conn->prepare("SELECT * FROM tbUser WHERE UserName = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $stored_hash = $row['UserPassword'];

        if (password_verify($pass, $stored_hash)) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['UserName']; // Store username in session
            header("location:../index.php");
            exit();
        } else {
            // --- DETAILED DEBUGGING FOR FAILED LOGIN ---
            $debug_info = "<b>Login Failed. Please check the details below:</b><br><br>";
            $debug_info .= "<b>Username Entered:</b> " . htmlspecialchars($user) . "<br>";
            $debug_info .= "<b>Stored Hash Length:</b> " . strlen($stored_hash) . " characters.<br>";

            if (strlen($stored_hash) < 60) {
                $debug_info .= "<br><b style='color:red;'>CRITICAL ERROR:</b> The password hash stored in the database is too short. It MUST be 60 characters long. <br><b>SOLUTION:</b> Go to phpMyAdmin, select `tbUser` table, change the `UserPassword` column type to `VARCHAR(255)`, and then run the `create_user.php` script again.";
            } else {
                $debug_info .= "<br>The password you entered is incorrect. <br><b>SOLUTION:</b> Please make sure you are using the correct password. To reset it to '123456', please run the `create_user.php` script again.";
            }
            $error = $debug_info;
        }
    } else {
        $error = "User '" . htmlspecialchars($user) . "' not found!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Login</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: pink; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        h2 { color: #333; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; margin-top: 10px; transition: background 0.3s; }
        button:hover { background: #0056b3; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="login-container">
<h2>Hotel Login</h2>

<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

<form method="post">
<input name="user" placeholder="Username" required>
<input type="password" name="pass" placeholder="Password" required>
<button name="login">Login</button>
</form>
</div>
</body>
</html>
