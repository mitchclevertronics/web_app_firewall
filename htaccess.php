<?php
/*
 * script for htaccess injection(backend)
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
require_once "libs/db.inc.php";
require_once "libs/waf_report.class.php";
$WR=new WafReport;

if($WR->isEditor()==false)die("No Access");

$filename=$_SERVER['DOCUMENT_ROOT']."/.htaccess";
$folder=trim(substr($_SERVER['PHP_SELF'],1,strrpos($_SERVER['PHP_SELF'],"/")-1));

if(isset($_POST['op'])&&isset($_POST['content']))
{
 $f=fopen($filename,"w");
 fwrite($f,$_POST['content']);
 fclose($f);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>		
</head>
<body>
<?php include_once 'include/header.php';?>   
		<h1 class='title'>Edit .htaccess for redirect code injection</h1>		
		<div class='box'>
				<strong><?php echo $filename;?></strong><br>
					
						File exists: <?php echo file_exists($filename)?'Yes':'No';?><br>		
						File writeble: <?php echo is_writable($filename)?'Yes':'No';?><br>	
						<div class='description'>	
								<ol>
										<li>Backup origin .htaccess file</li>
										<li>Copy the code from upper window to lower window to be first</li>
										<li>Save</li>
								</ol>		
						 <textarea class="inset textarea" rows='5'>
RewriteEngine On
SetEnvIf WAF_KEY "(.*)" HTTP_WAF_KEY=<?php echo $WR->waf_security_key;?>

RewriteCond $1 !\.(gif|jpg|png|ico|css|js|swf|wav|mp3|less|cur)
RewriteCond %{HTTP:WAF_KEY2} !<?php echo $WR->waf_security_key2;?>

RewriteCond %{REQUEST_URI} !<?php echo $folder;?>

RewriteRule ^(.*)$ <?php echo $folder;?>/waf.php [N,L]</textarea></div>
								<form action="" method="POST">
									<textarea name='content' rows='40' class="inset textarea"><?php echo file_exists($filename)?file_get_contents($filename):"";?></textarea>
						<input type="submit" name="op" value="Save" class="green_btn">
								</form>		
		</div>
</body>
</html>		