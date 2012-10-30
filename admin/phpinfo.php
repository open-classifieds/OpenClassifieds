<?php
require_once('access.php');
require_once('header.php');

?>
<div class="page-header">
	<h1><?php _e("PHP Info");?></h1>	
</div>

<?php
ob_start();                                                                                                        
phpinfo();                                                                                                     
$info = ob_get_contents();                                                                                         
ob_end_clean();                                                                                                    
echo preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
?>


<script>
$(document).ready(function() {
	$("table").addClass("table table-striped  table-bordered");
});
</script>

<?php
require_once('footer.php');
?>
