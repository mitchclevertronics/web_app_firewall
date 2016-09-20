<?php
session_start();
require_once "libs/db.inc.php";
require_once "libs/user.class.php";

$WU=new WafUser;
$WU->check_user_session();
$users=$WU->get_users();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
</head>
<body>
<?php include_once 'include/header.php';?>    
		<h1 class='title'>Users Management</h1>
		<div class='box'>
				<table width='100%' class="tbl">
					<tr>
							<th>Email</th>
							<th>Editor</th>
							<th>Active</th>
							<th>&nbsp;<?php if($WU->isEditor()):?><a href='edituser.php?id=0' class='add_user'>Add User</a><?php endif;?></th></tr>		
			<?php foreach($users as $u):?>
					<tr>
							<td><?php echo $u['email']?></td>
							<td><?php echo ($u['editor'])?'Yes':'No';?></td>
							<td><?php echo ($u['status'])?'Yes':'No';?></td>
							<td>
									<?php if($WU->isEditor()):?>
									<a href='edituser.php?id=<?php echo $u['id'];?>' class='add_user'>Edit</a>
									<?php endif;?>&nbsp;
							</td>
					</tr>		
			<?php endforeach;?>		
			</table>		
		</div>		
		<div class="box">
				<small>Only Editor can edit users and control permissions map.<br>Non Editor user can view statistics </small>
		</div> 
</body>
</html>