<?php
require_once('access.php');
require_once('header.php');
?>
<h2>Ocaku API KEY</h2>
<?php

$ocaku=new ocaku();

//Register new site
$data=array(
			'siteUrl'=>SITE_URL,
			'email'=>NOTIFY_EMAIL	
		 );
$ocaku->rememberKEY($data);

echo 'Check your email('.NOTIFY_EMAIL.') please';

require_once('footer.php');
?>