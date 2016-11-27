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
$logs=$WR->get_logs($_GET);
function get_page_link($page,$get)
{
	$get['wafpage']=$page;
	$link='';
	$parts=Array();
	foreach($get as $gn=>$gv)
		$parts[]=$gn.'='.$gv;
	return implode('&',$parts);
}
if(!isset($_GET['wafpage']))$_GET['wafpage']=1;
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
					<input type="submit" id="search_logs" value="Search">		
					<img src='assets/imgs/question.png' width="20" id="filter_help" title="Help">	
				</form>
		</div>
<div class="box_logs">
		<table class="logs_report" cellpadding="0" cellspacing="0">
				<caption>Found <?php echo $WR->logs_count;?> event</caption>
				<tr>
					<th><?php if($logs):?><input type="checkbox" class="chbx_all"><?php endif;?>&nbsp;</th>
					<th>Seg.ID</th>
					<th>Type</th>
					<th>Time</th>
					<th>URL</th>
					<th>IP</th>
					<th>Reason</th>
					
					
				</tr>		
		<?php if($logs):?>		
    <?php foreach($logs as $log):
			$content= json_decode(base64_decode($log['content']));
			$reason= htmlspecialchars(base64_decode($log['reason']));
		
	?>  
		<tr>
				<td><input type="checkbox" class="chbx" rel="<?php echo $log['id'];?>" name="chbx"></td>
				<td><?php if(!empty($log['sid'])):?><a href="map.php?approved=-1&bf=-1&use_type=-1&vars=-1&sid=<?php echo $log['sid']?>"><?php echo $log['sid']?></a><?php endif;?></td>
				<td nowrap><small><?php echo $WR->types_logs[$log['type']];?></small></td>
				<td><?php echo date('H:i d/m/Y',strtotime($log['created']));?></td>
				<td class="tooltip" title="<?php echo isset($content->request)?  str_replace('"','``',print_r($content->request,1)):'';?>">
					<a href="<?php echo htmlspecialchars($log['url']);?>" target="_blank"><?php echo htmlspecialchars($log['url']);?></a></td>
				<td class="tooltip" title="<?php echo str_replace('"','``',print_r($content->server,1));?>"><?php echo $log['ip'];?></td>
				<td class="tooltip" title="<?php echo $reason;?>"><?php echo substr($reason,0,150);?></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		</table>
	
	<?php if($WR->isEditor()):?>
		<div style="padding:3px;">
			<a href="javascript://" id="import" class="green_btn">Import Logs</a><input id="import-file" type="file"  style="display:none"/>&nbsp;	
			<?php if($logs):?>
				<input type="button" value="Export Logs" id="export" class="green_btn" title="export logs filtered by form"><a id="export_helper"></a>&nbsp;
				<input type="button" value="Delete Selected" id="delete_selected" class="red_btn" title="delete only selected logs by checkbox">&nbsp;
					<input type="button" value="Delete Logs" id="truncate_logs" class="red_btn" title="delete logs via form's filter">&nbsp;
			<?php endif;?>		
		</div>
	<?php endif;?>	
	<?php if($logs):?>
		<div class="pagging">
		<?php 
		$min=$_GET['wafpage']-3;
		if($min<1)$min=1;
		$max=$_GET['wafpage']+3;
		if($max>$WR->total_pages)$max=$WR->total_pages;
		?>		
		<?php if($_GET['wafpage']!=1):?><a href="logs.php?<?php echo get_page_link(1,$_GET);?>">&laquo;</a><?php endif;?>		
		<?php for($p=$min;$p<=$max;$p++):?>
		<?php if($p!=$_GET['wafpage']):?><a href="logs.php?<?php echo get_page_link($p,$_GET);?>"><?php endif;?>
			<?php echo $p;?>
		<?php if($p!=$_GET['wafpage']):?></a><?php endif;?>
		<?php endfor;?>
		<?php if($_GET['wafpage']!=$WR->total_pages):?><a href="logs.php?<?php echo get_page_link($WR->total_pages,$_GET);?>">&raquo;</a><?php endif;?>		
		</div>	
	<?php endif;?>
</div>    

<script>
  $(function() {
    $( "#from_date" ).datepicker({'dateFormat':'dd-mm-yy'});
		$( "#to_date" ).datepicker({'dateFormat':'dd-mm-yy'});
		
		//chbx_all
		$('.chbx_all').change(function (){
			if($('.chbx_all').is(":checked"))$('.chbx').prop('checked',true);
			else $('.chbx').prop('checked',false);
		});
		//delete selected
		$('#delete_selected').click(function  (){
			var ids=[];
			$("#loader").show(); 
			$('.chbx:checked').each(function (i,box){ids.push($(box).attr('rel'));});
			$.get('ajax.php?act=delete_logs&ids='+ids.join(","), {}, function() {
				location.reload(); 
			});
			
		});
		//truncate logs
		$('#truncate_logs').click(function (){
		 if(confirm("Delete logs filtered by form?"))
		 {
			$("#loader").show(); 
			var p=location.href.split("?");
			
			 $.get('ajax.php?act=truncate_logs&'+((typeof p[1]!='undefiend')?p[1]:''), {}, function() {
				location.reload(); 
			});
			
		 }else{
			return false;
		 }
		});
		//export
		$('#export').click(function (){
			if(confirm("Export filtered by form"))
			{
			var params=location.href.split("?");
			$("#loader").show(); 
			$.get('ajax.php?act=export_logs&'+((typeof params[1]!='undefined')?params[1]:''), {}, function(data) {
					var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data));
					var dlAnchorElem = $('#export_helper');
					dlAnchorElem.attr("href",dataStr);
					var d = new Date();
					var month=(d.getMonth()+1);
					if(month==13)month=1;
					dlAnchorElem.attr("download", "waf_logs."+d.getFullYear()+month+d.getDate()+".json");
					$("#loader").hide(); 
					dlAnchorElem[0].click();

					});
			}
		});	
		//import
		$('#import').click(function (){
				$('#import-file').click();
				$('#import-file').change(function (event){
					
					if(event.target.files.length>0)
					{
						var files=event.target.files;
						var reader = new FileReader();
							reader.readAsText(files[0], "UTF-8");
							reader.onload = function (evt) {
							$("#loader").show(); 
								$.ajax({
									url: 'ajax.php?act=import_logs',
									type: 'POST',
									data: evt.target.result,
									cache: false,
									dataType: 'json',
									processData: false, // Don't process the files
									contentType: false, // Set content type to false as jQuery will tell the server its a query string request
									success: function(data, textStatus, jqXHR)
									{
										$("#loader").hide(); 
										if(typeof data.error === 'undefined')
										{
											alert('Imported '+data.count+' items');
											location.reload();
										}else{alert('ERRORS: ' + data.error);}
									},
									error: function(jqXHR, textStatus, errorThrown){$("#loader").hide(); alert('ERRORS: ' + textStatus);}
								});

							}


					}
				});
			});	
			//help
			  $('#filter_help').click(function (){
					$('.legend_box').show();
				});
				$('#close_legends').click(function (){
					$('.legend_box').hide();
				});
  });

</script>
<!--VARS SINGLE MENU EOF-->  
<div id="loader"><img src="assets/imgs/loader.gif"></div>
<div class='legend_box'><img src="assets/imgs/x.png" class="x" id="close_legends" style="float:right;">
	Point cursor to URL field - for read full Request info<br>
	Point cursor to IP field - for read full Server info<br>	
		Point cursor to Reason fields - for read full Reason information<br>
	Click on Segment ID number for view segment in Access Map 
</div>
</body>
</html>