<?php
session_start();
require_once('../includes/config.php');//configuration file
//include to control access for admin in the app with the session
if (!isset($_SESSION['admin']) )// first you need to be logged
{
	header("Location: login.php");
	die();
}
?>
