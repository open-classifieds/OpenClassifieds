<?php
require_once('access.php');
require_once('header.php');

function accountCheck($key,$id=""){ //try to prevent duplicated accounts
	$ocdb=phpMyDB::GetInstance();    
    if (is_numeric($id)) $query="SELECT email FROM ".TABLE_PREFIX."accounts where (email='$key') and (idAccount <> $id) limit 1";
    else $query="SELECT email FROM ".TABLE_PREFIX."accounts where (email='$key') limit 1";
    $res=$ocdb->getValue($query,"none");
    
    return $res;	
}
$filter = '';
if ($_POST){
    $action=trim(cP("action"));
    $account_id=trim(cP("account"));
    $name=trim(cP("name"));
    $email=trim(cP("email"));
            
    if ($action=="new"){
        $account = new Account($email);
        if ($account->exists){
            echo "<div class='alert alert-success'>".T_("Account already exists")."</div>";
        }
        else {
            $password=generatePassword(8);
       
            if ($account->Register($name,$email,$password)){
                $token=$account->token();
    
                if ($account->Activate($token)){
                    $message='<p>'.T_("Your new account information").'</p>
                    <p><label>'.T_("Email").': '.$account->email.'</label><br/>
                    <label>'.T_("Password").': '.$password.'</label></p>';
    
                    $array_content[]=array("ACCOUNT", $account->name);
                    $array_content[]=array("MESSAGE", $message);
                            
                    $bodyHTML=buildEmailBodyHTML($array_content);
                            
                    sendEmail($email,T_("Your new account")." - ".SITE_NAME,$bodyHTML);//
         
                    echo "<div class='alert alert-success'>".T_("Account succesfully created")."</div>";
                } else echo "<div class='alert alert-success'>".T_("An unexpected error has occurred trying to confirm the account")."</div>";
            } else echo "<div class='alert alert-success'>".T_("An unexpected error has occurred trying to register the account")."</div>";
        }
    }
    if (is_numeric($account_id)){
        if ($action=="edit"){                
            if (!accountCheck($email,$account_id)){  //no exists update
                    $ocdb->update(TABLE_PREFIX."accounts","name='$name',email='$email'","idAccount=$account_id");
                    
                    $message='<p>'.T_("Your account has been updated").'</p>
                    <label>'.T_("Name").': '.$name.'</label></p><br/>                
                    <p><label>'.T_("Email").': '.$email.'</label>';
                    
                    $array_content[]=array("ACCOUNT", T_("User"));
                    $array_content[]=array("MESSAGE", $message);
                    
                    $bodyHTML=buildEmailBodyHTML($array_content);
                    
                    sendEmail($email,T_("Your account has been updated")." - ".SITE_NAME,$bodyHTML);//
                    
                    echo "<div class='alert alert-success'>".T_("Account succesfully updated")."</div>";
                    }
                    else echo "<div class='alert alert-success'>".T_("Account already exists")."</div>";
        }
    }
} else {
    $action=trim(cG("action"));
    $account_id=trim(cG("account"));
    $email=trim(cG("amail"));
    
    if (is_numeric($account_id)){
        if ($action=="activate"){
            $ocdb->update(TABLE_PREFIX."accounts","active=1","idAccount=$account_id");
            
            $action_notify=T_("Activated");
                    
            echo "<div class='alert alert-success'>".T_("Account successfully activated")."</div>";
        } elseif ($action=="deactivate"){ 
            $ocdb->update(TABLE_PREFIX."accounts","active=0","idAccount=$account_id");
                    
            //deactivate related posts
            $ocdb->update(TABLE_PREFIX."posts","isAvailable=0","email='$email'");
            if (CACHE_DEL_ON_POST) deleteCache();//delete cache
            
            $action_notify=T_("Deactivated");
                    
            echo "<div class='alert alert-success'>".T_("Account successfully deactivated")."</div>";
        } elseif ($action=="delete"){
            $ocdb->delete(TABLE_PREFIX."accounts","idAccount=$account_id");
                    
            //delete related posts
            $query="SELECT idPost FROM ".TABLE_PREFIX."posts
                        where email='$email'";
            $resultSearch=$ocdb->getRows($query);	
            if ($resultSearch){
                foreach ( $resultSearch as $row ){
                    $idPost=$row['idPost'];
                    deletePostImages($idPost);//delete images! and folder
                }
            }
            $ocdb->delete(TABLE_PREFIX."posts","email='$email'");
            if (CACHE_DEL_ON_POST) deleteCache();//delete cache
    
            $action_notify=T_("Removed");
                    
            echo "<div class='alert alert-success'>".T_("Account successfully removed")."</div>";
        }
                
        if ($action_notify!="" && $email!=""){
            $message='<p>'.T_("Your account has been updated").'</p><br/>
            <label>'.T_("Actions performed").': '.$action_notify.'</label></p>';
                    
            $array_content[]=array("ACCOUNT", T_("User"));
            $array_content[]=array("MESSAGE", $message);
                    
            $bodyHTML=buildEmailBodyHTML($array_content);
                    
            sendEmail($email,T_("Your account has been updated")." - ".SITE_NAME,$bodyHTML);//
        }
    }
}//if post
?>
<script type="text/javascript">
	function newAccount(){
		d = document.Account;
		d.account.value = "";
		d.name.value = "";
		d.email.value = "";
		d.action.value ="new";
		d.submitAccount.value ="<?php _e("New Account");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("New Account");?>";
		show("formAccount");
		location.href = "#formAccount";
	}	
	function editAccount(account, name, email){
		d = document.Account;
		d.account.value = account;
		d.name.value = name;
		d.email.value = email;
		d.action.value ="edit";
		d.submitAccount.value ="<?php _e("Update Account");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("Update Account");?>";
		show("formAccount");
		location.href = "#formAccount";
	}	
</script>

<div class="page-header">
	<?php if (!LOGON_TO_POST) echo T_('Accounts (Logon to Post) are disabled, please go to the settings to enable this feature.').'<br />';?>
	<h1><?php _e("Accounts list");?></h1>
	<button class="btn btn-primary pull-right" onclick="newAccount();return false;">
		<i class="icon-pencil icon-white"></i>
		<?php _e("New Account");?>
	</button>		
</div>

<div id="formAccount" style="display:none;">
	<div id="form-tab" class="form-tab"></div>
	<form class="well" name="Account" action="" method="post" onsubmit="return checkForm(this);">
		<fieldset>
			<p>
				<label><?php _e("Name");?></label>
				<input name="name" type="text" class="text-long" lang="false" onblur="validateText(this);" xml:lang="false" />
			</p>
			<p>
				<label><?php _e("Email");?></label>
				<input name="email" type="text" class="text-long" lang="false" onblur="validateText(this);" xml:lang="false" />
			</p>
			<input id="submitAccount" type="submit" value="<?php _e("Submit");?>" class="btn btn-primary" />
			<input type="submit" value="<?php _e("Cancel");?>" class="btn" onclick="hide('formAccount');return false;" />
			<input type="hidden" name="account" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>

<table class="table table-bordered">
	<thead>
	<tr>
		<th><?php _e("Active");?></th>
		<th><?php _e("Name");?></th>
		<th><?php _e("Email");?></th>
		<th><?php _e("Date Created");?></th>
		<th>&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php 
        if (cG("name")!=""){
            $filter.= " and a.name like '%".cG("name")."%' ";
        }
        if (cG("email")!=""){
            $filter.= " and a.email like '%".cG("email")."%' ";
        }
        
        $order="a.createdDate Desc";
        
        //pagination
        $query="SELECT count(a.idAccount) total	FROM ".TABLE_PREFIX."accounts a
                WHERE 1 $filter";
        $total_records = $ocdb->getValue($query);//query to get total of records
        $records_per_page = ITEMS_PER_PAGE;//how many records per page displayed
        $total_pages = ceil($total_records / $records_per_page);//total pages to display
        if ($page < 1 || $page > $total_pages) $page = 1;//controlling that the page have a valid value
        $offset = ($page - 1) * $records_per_page;//position where we need to display
        $limit = " LIMIT $offset, $records_per_page";//sql to attach IMPORTANT
        //end pagination
    
        $query="SELECT a.idAccount,a.name,a.email,a.createdDate,a.active FROM ".TABLE_PREFIX."accounts a
                WHERE 1 $filter
                order by $order $limit";
        //echo $query;
        $resultSearch=$ocdb->getRows($query,"assoc","none");				
    
        if ($resultSearch){
			foreach ( $resultSearch as $row ){
				$idAccount=$row['idAccount'];
				$name=$row['name'];//
				$email=$row['email'];//
				$insertDate=setDate($row['createdDate']);
				$active=$row['active'];//
				?>
	<tr>      
		<td><i class="icon-<?php if($active) echo "ok"; else echo "remove";?>"></i></td>
		<td><?php echo $name;?></td>
		<td><a href="mailto:<?php echo $email;?>"><?php echo $email;?></a></td>
		<td><?php echo $insertDate;?></td>
		<td>
        	<a class="btn btn-primary" onclick="editAccount('<?php echo $idAccount; ?>', '<?php echo $name;?>', '<?php echo $email;?>');return false;">
        		<i class="icon-edit icon-white"></i>
        	</a> 
			<?php if ($active){?>
           		<a class="btn btn-warning" onclick="return confirm('<?php _e("Deactivate");?>?');" href="?account=<?php echo $idAccount;?>&amp;amail=<?php echo $email;?>&amp;action=deactivate">
           			<i class="icon-remove icon-white"></i>
           		</a>
            <?php } else {?>
            	<a class="btn btn-success" onclick="return confirm('<?php _e("Activate");?>?');" href="?account=<?php echo $idAccount;?>&amp;amail=<?php echo $email;?>&amp;action=activate">
            		<i class="icon-ok icon-white"></i>
            	</a>
            <?php }?>
			<a class="btn btn-danger" onclick="return confirm('<?php _e("Delete");?>?');" href="?account=<?php echo $idAccount;?>&amp;amail=<?php echo $email;?>&amp;action=delete">
				<i class="icon-trash icon-white"></i>
			</a>
		</td>
	</tr>
	<?php 
			}
        }//end if check there's results
		else echo "<p>".T_("Nothing found")."</p>";
    ?>
    </tbody>
</table>
<div class="pagination">
<?php //page numbers
    if ($total_pages>1){
		$pag_title=$html_title." ".T_('Page')." ";
		
		$pag_url="/admin/accounts.php";

		//getting the url
		if(strlen(cG("name"))>=1 || strlen(cG("email"))>=1){
			$pag_url.='?name='.cG("name").'&email='.cG("email").'&page=';
		}
		else {
		   $pag_url.='?page=';
		}
		//////////////////////////////////
    
    	if ($page>1){
			echo "<li><a title='$pag_title' href='".SITE_URL.$pag_url."1'><i class='icon-step-backward'></i></a></li>";//First
			echo "<li><a title='".T_("Previous")." $pag_title".($page-1)."' href='".SITE_URL.$pag_url.($page-1)."'><i class='icon-backward'></i></a></li>";//previous
		}
			
		//pages loop
		for ($i = $page; $i <= $total_pages && $i<=($page+DISPLAY_PAGES); $i++) {//
		//for ($i = 1; $i <= $total_pages; $i++) {
	        if ($i == $page) echo "<li class='active'><a title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a></li>";//print the link
	        else echo "<li><a title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a></li>";//print the link
	    }
	    
    	if ($page<$total_pages){
		   	echo "<li><a href='".SITE_URL.$pag_url.($page+1)."' title='".T_("Next")." $pag_title".($page+1)."' ><i class='icon-forward'></i></a></li>";//next
		   	echo  "<li><a title='$pag_title$total_pages' href='".SITE_URL."$pag_url$total_pages'><i class='icon-step-forward'></i></a></li>";//End
		}
    }	
?>
</div>
<h4><?php _e("Search");?></h4>
<form name="ocForm" class="well" id="ocForm" action="" method="get">
	<fieldset>
        <p>
        	<label><?php _e("Name");?></label>
            <input name="name" type="text" class="text-long" value="<?php echo cG("name");?>" />
		</p>
		<p>
        	<label><?php _e("Email");?></label>
            <input name="email" type="text" class="text-long" value="<?php echo cG("email");?>" />
		</p>
		<input type="submit" value="<?php _e("Search");?>" class="btn" />
	</fieldset>
</form>
<?php
require_once('footer.php');
?>