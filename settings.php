<?php
/*
 * script for settings management
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
require_once "libs/db.inc.php";
require_once "libs/waf_report.class.php";
$WR=new WafReport;
if(isset($_POST['save_settings'])&&($WR->isEditor()))
{
 
 
 $WR->save_settings('waf_learn_status',isset($_POST['waf_learn_status'])?true:false);
 $WR->save_settings('waf_guard_status',isset($_POST['waf_guard_status'])?true:false);
 $WR->save_settings('url404',$_POST['url404']);
 $WR->save_settings('waf_security_key',$_POST['waf_security_key']);
 $WR->save_settings('waf_security_key2',$_POST['waf_security_key2']);
 $WR->save_settings('waf_bf',$_POST['bf']);
 $WR->save_settings('waf_bf_attempt',$_POST['bf_attempt']);
 $WR->save_settings('waf_bf_bantime',$_POST['bf_bantime']);
 $WR->reload_settings();
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
</head>
<body>
<?php include_once 'include/header.php';?>    
<div>
    <div class='status_window'>
				<form action="" method="POST">
				<table>
						<tr>
								<td colspan="2">
										<label>W.A.F. Status:</label>
            <label for="waf_learn_status">Learn:</label>
						<input type="checkbox" name="waf_learn_status" id="waf_learn_status" <?php if($WR->waf_learn_status):?> checked="checked"<?php endif;?> <?php if($WR->isEditor()):?> readonly<?php endif;?>>
            <label for="waf_guard_status">Guard:</label>
						<input type="checkbox" name="waf_guard_status" id="waf_guard_status" <?php if($WR->waf_guard_status):?> checked="checked"<?php endif;?> <?php if($WR->isEditor()):?> readonly<?php endif;?>>
								</td>
						</tr>
						<tr>
								<td><label>Security Key</label></td>
								<td><input type="text" name="waf_security_key" id="waf_security_key" value="<?php echo $WR->waf_security_key;?>" class="inset">
								</td>
						</tr>
						<tr>
								<td><label>Security Key2</label></td>
								<td><input type="text" name="waf_security_key2" id="waf_security_key2" value="<?php echo $WR->waf_security_key2;?>" class="inset"></td>
						</tr>
						<tr><td colspan="2"><input type="button" class="add_user" id="new_waf_security_key" value="Generate New Keys" style="width:200px;"></td></tr>
						<tr>
								<td><label>404 Page URL</label></td>
								<td><input type="text" name="url404" id="url404" value="<?php echo $WR->url404;?>" class="inset"></td>
						</tr>
						<tr>
								<td><label>Brute Force Frequency</label></td>
								<td><input type="text" name="bf" id="bf" value="<?php echo $WR->waf_bf;?>" class="inset"></td>
						</tr>
						<tr>
								<td><label>Brute Force Attempts</label></td>
								<td><input type="text" name="bf_attempt" id="bf_attempt" value="<?php echo $WR->waf_bf_attempt;?>" class="inset"></td>
						</tr>
						<tr>
								<td>Brute Force Ban Time:</td>
								<td><input type='text' name="bf_bantime" id="bf_bantime" value="<?php echo $WR->waf_bf_bantime;?>" size="4" class="inset"> days <font style="color:dimgray;font-size:12px;">(0 days - block always)</font></td>
						</tr>
						<tr>
								<td colspan="2"><input type="submit" value="Save" id="save_settings" name="save_settings"></td>
						</tr>
				</table>		
				</form>		
    </div>    
</div>    
<script>
$('#new_waf_security_key').click(function (){
 if(confirm("If you change Security Key, you need immidiatly change it in HTACCESS EDIT, just save new code instead of old one."))
 {
	  $.get( "ajax.php?act=generate_key", function( json ) {
         if(typeof(json.key)!='undefined')$('#waf_security_key').val(json.key);   		
				 if(typeof(json.key2)!='undefined')$('#waf_security_key2').val(json.key2);
		 },'json');
 }
});
</script>		
<!--VARS SINGLE MENU EOF-->   
</body>
</html>