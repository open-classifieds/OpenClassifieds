<?php 
require_once('access.php');
require_once('../includes/bootstrap.php');

if (regenerateHtaccess(SITE_URL))
{
	redirect(SITE_URL."/admin/settings.php?msg=".T_("Updated"));
}
else 
{
	die(T_("The configuration file")." '/.htaccess' ".T_("is not writable").". ".T_("Change its permissions and try again"));
}

?>