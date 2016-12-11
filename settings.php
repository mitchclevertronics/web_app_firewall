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
 $WR->save_settings('waf_learn_ip_only',isset($_POST['waf_learn_ip_only'])?true:false);
 $WR->save_settings('waf_learn_ip',$_POST['waf_learn_ip']);
 $WR->save_settings('waf_learn_ip_approve',isset($_POST['waf_learn_ip_approve'])?true:false);
 $WR->save_settings('waf_guard_status',isset($_POST['waf_guard_status'])?true:false);
 $WR->save_settings('waf_skip_ip',$_POST['waf_skip_ip']);
 $WR->save_settings('url404',$_POST['url404']);
 $WR->save_settings('webmaster_email',$_POST['webmaster_email']);
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
<style>
.settings_tbl th{
    font-weight:bold;
}
.settings_tbl th h2{
    display: table-cell;
    font-size:19px;
    margin:0;
    white-space:nowrap;
   
}
.settings_tbl th span{
    display: table-cell;
    width:100%;
    vertical-align: middle;
}
.settings_tbl th hr{
    color:black;
}
.settings_tbl th span hr{
    margin:3px 0 0 -1px;
    padding:0;
    color:black;
}
</style>
</head>
<body>
<?php include_once 'include/header.php';?>    
<div>
    <div class='status_window'>
	    <form action="" method="POST">
				<table class="settings_tbl">
                <tr><th colspan="2"><h2>W.A.F. Protection Status</h2><span><hr /></span></th></tr>
					<tr>
						<td align="center">
							
                            <label for="waf_learn_status">Learn:</label>
						    <input type="checkbox" name="waf_learn_status" id="waf_learn_status" <?php if($WR->waf_learn_status):?> checked="checked"<?php endif;?> <?php if($WR->isEditor()):?> readonly<?php endif;?>>
                        </td>
                        <td align="center">    
                            <label for="waf_guard_status">Guard:</label>
						    <input type="checkbox" name="waf_guard_status" id="waf_guard_status" <?php if($WR->waf_guard_status):?> checked="checked"<?php endif;?> <?php if($WR->isEditor()):?> readonly<?php endif;?>>
						</td>
					</tr>   
                    <tr class="waf_learn_ip_only_tr" <?php if(!$WR->waf_learn_status):?> style="display:none"<?php endif;?>>
                        <td>
                            <label for="waf_learn_ip_only">Learn only from IPs:</label>
                            <input type="checkbox" id="waf_learn_ip_only" name="waf_learn_ip_only" <?php if($WR->waf_learn_ip_only):?> checked="checked"<?php endif;?> <?php if($WR->isEditor()):?> readonly<?php endif;?>/>
                        </td>
                        <td>  
                            <div class="waf_learn_ip_approve_div" <?php if(!$WR->waf_learn_ip_only):?> style="display:none"<?php endif;?>>
                            <label for="waf_learn_ip_approve">Approve Immediately</label>  
                            <input type="checkbox" name="waf_learn_ip_approve" id="waf_learn_ip_approve" <?php if($WR->waf_learn_ip_approve):?> checked="checked"<?php endif;?> <?php if($WR->isEditor()):?> readonly<?php endif;?>/> 
                            </div>
                        </td>
                    </tr>
                    <tr class="waf_learn_ip_tr" <?php if(!$WR->waf_learn_ip_only):?> style="display:none"<?php endif;?>>
                        <td>
                            <label for="waf_learn_ip">Allowed IPs to learn:<br /><small>(comma separated)</small></label>
                        </td>
                        <td>   
                            <textarea id="waf_learn_ip" name="waf_learn_ip"  class="inset"/><?php echo $WR->waf_learn_ip;?></textarea> 
                        </td>
                    </tr>
                    <tr>
                        <td><label for="waf_skip_ip">IPs WhiteList:<br /><small>Skips Learn\Guard Mode.</small></label></td>
                        <td><textarea id="waf_skip_ip" name="waf_skip_ip" class="inset"><?php echo $WR->waf_skip_ip;?></textarea></td>
                    </tr>
                    <tr><th colspan="2"><h2>Security Keys</h2><span><hr /></span></th></tr>    
					<tr>
						<td><label>Security Key</label></td>
						<td><input type="text" name="waf_security_key" id="waf_security_key" value="<?php echo $WR->waf_security_key;?>" class="inset"></td>
					</tr>
					<tr>
							<td><label>Security Key2</label></td>
							<td><input type="text" name="waf_security_key2" id="waf_security_key2" value="<?php echo $WR->waf_security_key2;?>" class="inset"></td>
					</tr>
					<tr><td colspan="2"><input type="button" class="add_user" id="new_waf_security_key" value="Generate New Keys" style="width:200px;"></td></tr>
                    
                    <tr><th colspan="2"><h2>Brute Force</h2><span><hr /></span></th></tr>
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
					
                    <tr><th colspan="2"><h2>Customization</h2><span><hr /></span></th></tr>
                    <tr>
                            <td><label>404 Page URL</label></td>
                            <td><input type="text" name="url404" id="url404" value="<?php echo $WR->url404;?>" class="inset"></td>
                    </tr>
                    <tr>
                        <td><label>Webmaster Email</label><br><small>(showed on 404 Page)</small></td>
                            <td><input type="text" name="webmaster_email" id="webmaster_email" value="<?php echo $WR->webmaster_email;?>" class="inset" placeholder="test@test.com"></td>
                    </tr>
                    <tr><th colspan=2><hr /></th></tr>
                    <tr>
                            <td colspan="2"><input type="submit" value="Save" id="save_settings" name="save_settings" class="green_btn"></td>
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
//learn status changed
$('#waf_learn_status').change(function (event){
     if($(event.target).is(":checked"))
     {
         $('.waf_learn_ip_only_tr').show();
        
     }else{
          $('.waf_learn_ip_only_tr').hide();
          $('#waf_learn_ip_only').attr('checked',false);
          $('.waf_learn_ip_tr').hide();
          
     }
});

$('#waf_learn_ip_only').change(function (event){
     if($(event.target).is(":checked"))
     {
        $('.waf_learn_ip_tr').show();
        $('.waf_learn_ip_approve_div').show();
     }else{
        $('.waf_learn_ip_tr').hide();
        $('.waf_learn_ip_approve_div').hide();
        $('.waf_learn_ip_approve').attr('checked',false);
     }
});
</script>		
<!--VARS SINGLE MENU EOF-->   
</body>
</html>