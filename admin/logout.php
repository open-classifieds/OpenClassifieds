<?php
session_start();

unset($_SESSION['admin']);
//expire autologin cookie
setcookie('oc_admin','', time()-3600);

session_destroy();

header('Location: login.php');//redirect header
die();
?>
