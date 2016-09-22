<?php
/*
 * script for change user password
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();


require_once "libs/db.inc.php";

require_once "libs/user.class.php";

$WU=new WafUser;
$WU->check_user_session();
$WU->run_chg_pass_if();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>		
</head>
<body>
<?php include_once 'include/header.php';?>    
		<h1 class='title'>Change Your Password</h1>
		<div class='box'>
				<center>
				<?php if(!empty($WU->error))echo '<div style="color:red">'.$WU->error.'</div>';?>
				<form action="" method='POST'>
				<input type='password' name='old_pass' placeholder='Old Password' value='<?php if(isset($_POST['old_pass']))echo $_POST['old_pass'];?>' class='inset'><br>
								<input type='password' name='pass' placeholder='New Password' class='inset'><br>
								<input type='password' name='pass1' placeholder='Confirm Password' class='inset'><br>
				<input type='submit' value='change' id="change_button">
				</form>	
				</center>		
		</div>
</body>
</html>