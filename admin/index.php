<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: dashboard.php');  // Zaten giriş yaptıysa admin paneline yönlendir
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="main">

<div class="click">
        <a href="/index.php">
            <i class="bi bi-arrow-left"></i>
        </a>
</div>

<div class="main-div">

            <h1>Log in</h1>
            <form action="process_login.php" method="POST">
                <input type="text" placeholder="Enter your username" name="username" id="username" required>
                <input type="password" placeholder="Enter your password" name="password" id="password" required>
                <button type="submit">Log in</button>
            </form>

            <div class="div">
                <div class="remember">
                    <input type="checkbox" id="rem">
                    <label for="rem">Remember Me</label>
                </div>

                <div class="forgot">
                    <a href="/forgot.php">Forgot Password?</a>
                </div>
            </div>

</div>


</div>


</body>
</html>
