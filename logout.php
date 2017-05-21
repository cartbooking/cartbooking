<?php
$expiry = time() - 3600;
unset($_COOKIE["login"]);
setcookie("login", "", $expiry);
unset($_COOKIE["city"]);
setcookie("city", "", $expiry);
header("Location:index.php");
exit();
