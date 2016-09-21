<?php
/* 
 * Remind Password Managament 
 * This product includes PHP software, freely available from <http://www.php.net/software/>
 * Author: Roman Shneer romanshneer@gmail.com
 */
session_start();
require_once "libs/db.inc.php";
require_once "libs/user.class.php";

$WU=new WafUser;

if(isset($_POST['email']))
{
 
	 $WU->remind_password($_POST['email']);
	 
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
</head>
<body>
		<div class='header'></div> 
		<h1 class='title'>Remind Password</h1>
		
				<div class='box'>	
						<center>	
						<?php if(isset($_GET['sended'])):?>
								<div>We sent Email with instructions for remind password. </div>		
						<?php else:?>		
						<form action="" method='POST'>
						 <?php if(!empty($WU->error))echo '<div style="color:red">'.$WU->error.'</div>';?>		
						 <input type='text' name='email' placeholder='Enter email' class='inset'>
						 <input type='submit' value='Remind' id='login_button'>		
						
						 </form>
						<br><a href="login.php" class="add_user">Login</a>				
						<?php endif;?>		
						</center>		
				</div>			
		
</body>
</html>