<?php
/*
 * script for bad requests logs management
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
require_once "libs/db.inc.php";
require_once "libs/waf_report.class.php";

$WR=new WafReport;

if(($WR->isEditor())&&(isset($_GET['reset'])))
{
 $WR->truncate_logs();
 header("Location:logs.php");
 exit();
}
$logs=$WR->get_logs($_GET);
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
</head>
<body>
<?php include_once 'include/header.php';?>    
		<div class="logs_search_form" style="text-align: center;background:#fff;">
				<form action="" method="GET">	
					SegmentID: <input type="text" name="sid" size="3" class="inset" value="<?php echo isset($_GET['sid'])?$_GET['sid']:'';?>">
					Type: <select name="type" class="inset" >
						<option value="0">All</option>
						 <?php foreach($WR->types_logs as $opt=>$optval):?>
						<option value="<?php echo $opt;?>" <?php if(isset($_GET['type'])&&($_GET['type']==$opt)):?> selected<?php endif;?>><?php echo $optval;?></option>
						 <?php endforeach;?>
						  </select> 	
					
					From Date: <input type="text" id="from_date" size="8"  name="from_date" size="10" class="inset" value="<?php echo isset($_GET['from_date'])?$_GET['from_date']:'';?>" readonly>
					To Date: <input type="text" id="to_date"  size="8" name="to_date" size="10" class="inset" value="<?php echo isset($_GET['to_date'])?$_GET['to_date']:'';?>" readonly>
					 IP:		<input type="text" name="ip" size="10" class="inset" value="<?php echo isset($_GET['ip'])?$_GET['ip']:'';?>">
					URL: <input type="text" name="url" size="37"  class="inset" value="<?php echo isset($_GET['url'])?$_GET['url']:"";?>">		
					<input type="hidden" name="page" value="1" class="inset">
					<input type="submit"	id="search_logs" value="Search">	
					<a href="logs.php" class="add_user">Renew</a>		
					<?php if($WR->isEditor()):?>
					<a href="javascript://" style='float:right' title="Remove all Bag Request Statistics" id="truncate_logs">
							<img src="assets/imgs/roger.png" width="20" alt="Remove all Bag Request Statistics">
					</a>
					<?php endif;?>
				</form>
		</div>
<div class="box_logs">
		<table class="logs_report" cellpadding="0" cellspacing="0">
				<caption>Found <?php echo $WR->logs_count;?> event</caption>
				<tr>
						<th>Seg.ID</th>
						<th>Type</th>
						<th>Time</th>
						<th>URL</th>
						<th>IP</th>
						<th>Reason</th>
				</tr>		
		<?php if($logs):?>		
    <?php foreach($logs as $log):
			$content= json_decode(base64_decode($log['content']));?>  
		<tr>
				<td><?php if(!empty($log['sid'])):?><a href="map.php?approved=-1&bf=-1&use_type=-1&vars=-1&sid=<?php echo $log['sid']?>"><?php echo $log['sid']?></a><?php endif;?></td>
				<td nowrap><small><?php echo $WR->types_logs[$log['type']];?></small></td>
				<td><?php echo date('H:i d/m/Y',strtotime($log['created']));?></td>
				<td class="tooltip" title="<?php echo isset($content->request)?  str_replace('"','``',print_r($content->request,1)):'';?>"><?php echo htmlspecialchars($log['url']);?></td>
				<td class="tooltip" title="<?php echo str_replace('"','``',print_r($content->server,1));?>"><?php echo $log['ip'];?></td>
				<td class="tooltip" title="<?php echo $log['reason'];?>"><?php echo substr($log['reason'],0,150);?></td>
				<td></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		</table>
		<div class="pagging">
		<?php 
		$min=$_GET['page']-3;
		if($min<1)$min=1;
		$max=$_GET['page']+3;
		if($max>$WR->total_pages)$max=$WR->total_pages;
		?>		
		<?php if($_GET['page']!=1):?><a href="logs.php?<?php echo get_page_link(1,$_GET);?>">&laquo;</a><?php endif;?>		
		<?php for($p=$min;$p<=$max;$p++):?>
		<?php if($p!=$_GET['page']):?><a href="logs.php?<?php echo get_page_link($p,$_GET);?>"><?php endif;?>
			<?php echo $p;?>
		<?php if($p!=$_GET['page']):?></a><?php endif;?>
		<?php endfor;?>
		<?php if($_GET['page']!=$WR->total_pages):?><a href="logs.php?<?php echo get_page_link($WR->total_pages,$_GET);?>">&raquo;</a><?php endif;?>		
		</div>		
</div>    
<script>
  $(function() {
    $( "#from_date" ).datepicker({'dateFormat':'dd-mm-yy'});
		$( "#to_date" ).datepicker({'dateFormat':'dd-mm-yy'});
		
		$('#truncate_logs').click(function (){
		 if(confirm("Sure want delete all Bad Requests? Statistica impposible restore after that."))
		 {
			document.location='?reset=true';
		 }else{
			return false;
		 }
		});
  });

</script>
<!--VARS SINGLE MENU EOF-->   
</body>
</html>