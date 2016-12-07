<?php
/*
 * script for management blacklisted ips (from bruteforce)
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
require_once "libs/db.inc.php";
require_once "libs/waf_report.class.php";

$WR=new WafReport;
if(($WR->isEditor())&&(isset($_GET['act'])&&($_GET['act']=='remove')))
{
 $WR->delete_blacklist($_GET['id']);
 header("Location:blacklist.php");
 exit();
}
if(($WR->isEditor())&&(isset($_GET['act'])&&($_GET['act']=='addip')))
{
 $WR->add_ip2blacklist($_POST['ip']);
 header("Location:blacklist.php");
 exit();
}
function get_page_link($page,$get)
{
	$get['page']=$page;
	$link='';
	$parts=Array();
	foreach($get as $gn=>$gv)
		$parts[]=$gn.'='.$gv;
	return implode('&',$parts);
}
if(!isset($_GET['page']))$_GET['page']=1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>	
<script>
function validate_ip(){
	var ip=$('#ip').val();
	if(ip.match(/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/)==null)
	{
		alert("Its not IP!");
		$('#ip').focus();
	}else{
		if(ip=='127.0.0.1')
		{
		alert("Dont do it! You stop access from local address: 127.0.0.1");	
		$('#ip').focus();
		}else{
		$('#ipform').submit();
		}
	}
}
  $(function() {
    $( "#from_date" ).datepicker({'dateFormat':'dd-mm-yy'});
	$( "#to_date" ).datepicker({'dateFormat':'dd-mm-yy'});
	
  });

</script>
</head>
<body>
<?php include_once 'include/header.php';?>
<?php if(isset($_GET['act'])&&($_GET['act']=='ipform')):?>	
<div class="box_logs blacklist_page"  style="text-align: center;width:300px;">
	<form action="blacklist.php?act=addip" method='POST' id="ipform">
	<label for='ip'>IP:</label><input type='text' name='ip' id='ip'>
		<input type='button' value='save' id='save_ip' class="green_btn" onclick="validate_ip();">	
	</form>	
</div>	
<?php else: ?>	
<?php $bls=$WR->get_blacklist($_GET); ?>	
		<div class="logs_search_form" style="text-align: center;background:#fff;">
				<form action="" method="GET">	
						IP:		<input type="text" name="ip" size="10" class="inset" value="<?php echo isset($_GET['ip'])?$_GET['ip']:'';?>">
						SegmentID <input type="text" name="sid" size="3" class="inset" value="<?php echo isset($_GET['sid'])?$_GET['sid']:'';?>">
						From Date: <input type="text" id="from_date" size="8"  name="from_date" size="10" class="inset" value="<?php echo isset($_GET['from_date'])?$_GET['from_date']:'';?>" readonly>
						To Date: <input type="text" id="to_date"  size="8" name="to_date" size="10" class="inset" value="<?php echo isset($_GET['to_date'])?$_GET['to_date']:'';?>" readonly>
					 
					
					<input type="hidden" name="page" value="1" class="inset">
					<input type="submit"	id="search_logs" value="Search">		
				</form>
		</div>
<div class="box_logs">
		<table class="logs_report" cellpadding="0" cellspacing="0">
				<caption>Found <?php echo $WR->logs_count;?> blacklist records</caption>
				<tr>
						<th>SegmentID</th>
						<th>IP</th>
						<th>Time</th>
						<th><a href="?act=ipform" class="green_btn">add IP</a></th>
				</tr>		
		<?php if($bls):?>		
    <?php foreach($bls as $bl):?>  
		<tr>
				<td><?php echo $bl['sid']?> <?php if(!empty($bl['sid'])):?>:&nbsp;<a href="map.php?sid=<?php echo $bl['sid']?>">map</a><?php endif;?><?php if(!empty($bl['sid'])):?>&nbsp;<a href="logs.php?sid=<?php echo $bl['sid']?>">logs</a><?php endif;?></td>
				<td><?php echo $bl['ip'];?></td>
				<td><?php echo date('H:i d/m/Y',strtotime($bl['created']));?></td>
				<td><a href="?act=remove&id=<?php echo $bl['id']?>" class="red_btn">remove</a></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		</table>
		<?php if($WR->total_pages):?>
		<div class="pagging">
		<?php 
		$min=$_GET['page']-3;
		if($min<1)$min=1;
		$max=$_GET['page']+3;
		if($max>$WR->total_pages)$max=$WR->total_pages;
		?>		
		<?php if($_GET['page']!=1):?><a href="blacklist.php?<?php echo get_page_link(1,$_GET);?>">&laquo;</a><?php endif;?>		
		<?php for($p=$min;$p<=$max;$p++):?>
		<?php if($p!=$_GET['page']):?><a href="blacklist.php?<?php echo get_page_link($p,$_GET);?>"><?php endif;?>
			<?php echo $p;?>
		<?php if($p!=$_GET['page']):?></a><?php endif;?>
		<?php endfor;?>
		<?php if($_GET['page']!=$WR->total_pages):?><a href="blacklist.php?<?php echo get_page_link($WR->total_pages,$_GET);?>">&raquo;</a><?php endif;?>		
		</div>		
		<?php endif;?>
</div>    
<!--VARS SINGLE MENU EOF-->   
<?php endif;?>
</body>
</html>