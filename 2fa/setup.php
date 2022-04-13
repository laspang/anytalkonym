<?php
require "../class/User.php";
$user = new User();
$user->loginStatus();
?>
<!DOCTYPE html>

<head></head>

<body>
    <h1>2FA setup</h1>
    <h2>Details:</h2>
    <p>Secret: <?php echo $user->gentotp($_SESSION["userid"]);?>
    </p>
    <ul>
        <li>Time:30sec</li>
        <li>Hash:sha512</li>
        <li>digits:10</li>
    </ul>
    <h2>Verify:</h2>
    <form action="setupver.php" method="POST">
        <label for="code">2FA code:</label><input id="code" type="text" name="code">
    </form>
</body>