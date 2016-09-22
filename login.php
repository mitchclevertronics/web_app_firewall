<?php
/*
 * script for user login
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
require_once "libs/db.inc.php";
require_once "libs/user.class.php";

$WU=new WafUser;

if(isset($_POST['email'])&&isset($_POST['pass']))
{
 
	 $WU->auth_user($_POST['email'],$_POST['pass']);
	 
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
</head>
<body>
		<div class='header'></div> 
		<h1 class='title'>Wellcome to W.A.F.</h1>
		
				<div class='box'>	
						<center>	
						<form action="" method='POST'>
				<?php if(!empty($WU->error))echo '<div style="color:red">'.$WU->error.'</div>';
					?>		
					<input type='text' name='email' placeholder='Enter email' class='inset'>
					<input type='password' name='pass' placeholder='Password' class='inset'>
						<input type='submit' value='login' id='login_button'>		
						
						</form>
						<br><a href="remind_password.php" class="add_user">Remind password</a>				
						</center>		
				</div>			
		
</body>
</html>