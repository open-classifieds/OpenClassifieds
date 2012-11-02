<?php
require_once('includes/bootstrap.php');

//START PAYPAL IPN

//manual checks
if (!is_numeric(cP('item_number'))) paypal::report_problem('Not any idItem.');
else $idItem=cP('item_number');


///retrieve info for the item in DB
$query="select password,idCategory
		from ".TABLE_PREFIX."posts p
		where idPost=$idItem and isConfirmed=0  Limit 1";

$post_password='';
$idCategory=0;

$post_result=$ocdb->query($query);
if (mysql_num_rows($post_result)){
	$post_row=mysql_fetch_assoc($post_result);
	
	$post_password=$post_row["password"];
	$idCategory=$post_row["idCategory"];
}
else paypal::report_problem('Could not find the Item in DB.');//not found
		
$amount = (float)PAYPAL_AMOUNT;
if (PAYPAL_AMOUNT_CATEGORY){
	$query="select price from ".TABLE_PREFIX."categories where idCategory=$idCategory Limit 1";
    $result=$ocdb->query($query);
    if (mysql_num_rows($result)){
		$row=mysql_fetch_assoc($result);
		
		if (is_numeric($row["price"]))
			if ((float)$row["price"] != 0)
				$amount=(float)$row["price"];
	}
}

if ((float)cP('mc_gross')==$amount && cP('mc_currency')==PAYPAL_CURRENCY && (cP('receiver_email')==PAYPAL_ACCOUNT || cP('business')==PAYPAL_ACCOUNT)){//same price ,  currency and email no cheating ;)

	if (paypal::validate_ipn()) confirmPost($idItem,$post_password); //payment succeed and we confirm the post ;)     
	 
	else{
	    // Log an invalid request to look into 
	    // PAYMENT INVALID & INVESTIGATE MANUALY!
	    $subject = 'Invalid Payment';
	    $message = 'Dear Administrator,<br />
	    A payment has been made but is flagged as INVALID.<br />
	    Please verify the payment manualy and contact the buyer. <br /><br />Here is all the posted info:';
	    sendEmail(PAYPAL_ACCOUNT,$subject,$message.'<br />'.print_r($_POST,true));
	} 

}
//trying to cheat....
else paypal::report_problem('Trying to fake the post data');	

?>
    
