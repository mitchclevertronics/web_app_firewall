<?php
/*
 * script for reset password
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
require_once "libs/db.inc.php";
require_once "libs/user.class.php";

$WU=new WafUser;

if(isset($_GET['key']))
{
 
	$user=$WU->auth_user_by_rmn_pass($_GET['key']);
	if(($user)&&isset($_POST['pass']))
	{
	 $WU->run_chg_pass_if4reset($user); 
	}
	
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
</head>
<body>
		<div class='header'></div> 
		<?php if(isset($_GET['pass_changed'])):?>
					<h1 class='title'>Password Successfully changed</h1>
					<div class='box'>
							<center>Please login: <a href="login.php" class="add_user">Click here</a></center>
					</div>		
		<?php else:?>
					<?php if($user):?>
					<h1 class='title'>Change Your Password</h1>
					<div class='box'>
							<center>
							<?php if(!empty($WU->error))echo '<div style="color:red">'.$WU->error.'</div>';?>
							<form action="" method='POST'>
											<input type='password' name='pass' placeholder='New Password' class='inset'><br>
											<input type='password' name='pass1' placeholder='Confirm Password' class='inset'><br>
							<input type='submit' value='change' id="change_button">
							</form>	
							</center>		
					</div>			
					<?php else:?>
					<h1 class='title'>Guru you never know</h1>
					<?php endif;?>
		<?php endif;?>
		
		
		
</body>
</html>