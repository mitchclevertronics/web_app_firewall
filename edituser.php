<?php
session_start();
#require_once "libs/config.inc.php";

require_once "libs/db.inc.php";

require_once "libs/user.class.php";

$WU=new WafUser;
$WU->check_user_session(true);

$user=$WU->get_user($_GET['id']);
if(isset($_POST['id']))
{
	$WU->save_user($_POST);
	header('Location:users.php');
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
<?php /*		
<script src="assets/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/style.css">
*/?>
</head>
<body>
<?php include_once 'include/header.php';?>    
		<h1 class='title'>Edit User</h1>
		<div class='box'>
				<center>
				<form action="" method="POST">
				<input type='hidden' id='id' name='id' value='<?php echo $user['id'];?>'>
						<input type='text' name='email' id='email' placeholder='User Email' value="<?php echo $user['email'];?>" class="inset" <?php if($user['id']):?> readonly<?php endif;?>><br>
				<label for='editor'>Can Edit:</label>
				<input type='checkbox' name='editor' id='editor' <?php if($user['editor']):?> checked='checked'<?php endif;?>>
				<label for='editor'>Active:</label>
				<input type='checkbox' name='status' id='status' <?php if($user['status']):?> checked='checked'<?php endif;?>><br>				
				<input type='password' id='pass' name='pass' placeholder='Password' class="inset"><br>
				<input type='password' id='pass1' name='pass1' placeholder='Confirm Password' class="inset"><br>
				<input type='submit' value='Save' id="save_user">				
				</form>
				</center>		
		</div>		
</body>
<script>
var WEU={};	
WEU.init=function(){
	WEU.init_save_btn();
};
WEU.init_save_btn=function (){
	$('#save_user').click(function (){
		
	return WEU.valid_user_form();
	});
};
WEU.valid_user_form=function (){
	if($('#email').val().length<3)
	{
		alert('Please set Email');
		return false;
	}
	if($('#id').val()==0)
	{	
	if($('#pass').val().length<6)
	{
		alert('Please set Strong Password');
		return false;
	}
	}
	if($('#pass').val()!=$('#pass1').val())
	{
		alert('Password not confirmed');
		return false;
	}
	return true;
};





WEU.init();
</script>		
</html>