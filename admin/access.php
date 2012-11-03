<?php
session_start();
require_once('../includes/config.php');//configuration file



//include to control access for admin in the app with the session

	//auto login  / remember me
	if(isset($_COOKIE['oc_admin']) && !isset($_SESSION['admin']))
	{
		//verify that the cookie is good
		if($_COOKIE['oc_admin'] == md5(ADMIN+ADMIN_PWD))
		{
			//renew the cookie
			setcookie('oc_admin',md5(ADMIN+ADMIN_PWD), time()+60*60*24*30);
			//we log you ;)
			$_SESSION['admin'] = ADMIN;
		}
	}


	if (!isset($_SESSION['admin'])) //no auto login no session so login!
	{
		header("Location: login.php");
		die();
	}


//if you are here is because there's a session or autologin
?>
