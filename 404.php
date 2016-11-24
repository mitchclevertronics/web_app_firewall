<?php
session_start();
require_once "libs/db.inc.php";
$config=DB::get_config();
$db=new DB($config['db_host'],$config['db_name'],$config['db_user'],$config['db_pass']);
$result=$db->ROW_Q("SELECT value FROM waf_settings WHERE name='webmaster_email'");
$web_email=  isset($result['value'])?$result['value']:false;
?><!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>404: Content not Found</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    <center>
        <h1>404 Error</h1>
        <p style="color:red">The content not found or Access Blocked by <a href="https://github.com/shaman33/web_app_firewall/" target="_blank">Web App. Firewall</a> </p>
		<?php if($web_email):?>
		<p>If a page is blocked not because of a criminal activity , please notify the administrator about bug.
			<br><a href="mailto:<?php echo $web_email;?>"><?php echo $web_email;?></a></p>
		<?php endif;?>
    </center>    
    </body>
</html>
