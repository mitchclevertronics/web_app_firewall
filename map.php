<?php
/*
 * script for map access management
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
#require_once "libs/config.inc.php";

require_once "libs/db.inc.php";

require_once "libs/waf_report.class.php";

$WR=new WafReport;
$get=$_GET;
if(!isset($get['sid']))$get['sid']='';
if(!isset($get['approved']))$get['approved']=-1;
if(!isset($get['bf']))$get['bf']=-1;
if(!isset($get['use_type']))$get['use_type']=-1;
if(!isset($get['vars']))$get['vars']=-1;
if(!isset($get['vars_approved']))$get['vars_approved']=-1;

$segments=$WR->get_segments_tree2($get);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
<script src="assets/js/musclesoft-jquery-connections/jquery.connections.js"></script>		
<script src="assets/js/waf_map.js"></script>
<style>
.body_pencil{
    //cursor: url("assets/imgs/pencil_cursor.png"), cell;
	cursor: url("assets/imgs/green.png"), cell;
}
.body_eraser{
    cursor: url("assets/imgs/red.png"), crosshair;
}
.body_hand{
    cursor:pointer;
}
</style>
</head>
<body>
<?php include_once 'include/header.php';?>           
<?php if($segments):?> 
<div class="tree_house">
		
    <div id="seg_tree">    
     <?php echo $WR->draw_segments_tree($segments,1); ?>
    </div>
    <!--div id="tools_panel">
            <img src="assets/imgs/green.png" width="40" id="pencil">
            <img src="assets/imgs/red.png" width="40" id="eraser">
            <img src="assets/imgs/edit.png" width="40" id="edit_form" title="Click For Edit Selected">
			<img src="assets/imgs/vars.png" width="40" title="Click For Edit Global Variables" id="edit_global_vars" >
			  
              
    </div-->    
</div>    
<?php endif;?>  
	<div class='filter_box'>
		<form action="map.php" method="GET">
		<strong>Show Segments only:</strong>
		<fieldset class='filter_fieldset'>
			<div class='filter_fieldset_header'>Status</div>
			<select name="approved">
				<?php foreach(array(-1=>'all',0=>'new',1=>'approved') as $k=>$v):?>
				<option value="<?php echo $k;?>" <?php if($get['approved']==$k):?> selected<?php endif;?>><?php echo $v;?></option>
				<?php endforeach;?>
			</select>
		</fieldset>
		<fieldset class='filter_fieldset'>
		 <div class='filter_fieldset_header'>BF</div>		
		 <select name="bf">
			 <?php foreach(array(-1=>'all',0=>'Off',1=>'On') as $k=>$v):?>
				<option value="<?php echo $k;?>" <?php if($get['bf']==$k):?> selected<?php endif;?>><?php echo $v;?></option>
			<?php endforeach;?>
		 </select>
		</fieldset>
		<fieldset class='filter_fieldset'>
		<div class='filter_fieldset_header'>Rule Usage:</div>		
		<select name="use_type">
			<?php foreach(array(-1=>'all',0=>'Static',1=>'Rule') as $k=>$v):?>
				<option value="<?php echo $k;?>" <?php if($get['use_type']==$k):?> selected<?php endif;?>><?php echo $v;?></option>
			<?php endforeach;?>
		</select>		
		</fieldset>
		<fieldset class='filter_fieldset'>
		<div class='filter_fieldset_header'>Contains Vars:</div>		
		<select name="vars">
			<?php foreach(array(-1=>'all',0=>'Not contains',1=>'Contains Any',10=>'Contains New',11=>'Contains Approved') as $k=>$v):?>
				<option value="<?php echo $k;?>" <?php if($get['vars']==$k):?> selected<?php endif;?>><?php echo $v;?></option>
			<?php endforeach;?>
		</select>
		</fieldset>	
		<fieldset class='filter_fieldset'>
				<input type="text" placeholder="Segment ID" id="filter_segment_id" name="sid" style="width:80px;" value="<?php echo $get['sid'];?>">		
		</fieldset>
		<input type="submit" value="Go" class="green_btn">
		</form>
		<img src='assets/imgs/question.png' width="20" id="filter_help" title="Help">
		<div class="bottom_tools">
			<a href="javascript://" id="edit_global_vars" class="green_btn" title="Click For Edit Global Variables">Global Vars</a><a id="export_helper"></a>&nbsp;
			<a href="javascript://" id="export" class="green_btn">Export Map</a><a id="export_helper"></a>&nbsp;	
			<a href="javascript://" id="import" class="green_btn">Import Map</a><input id="import-file" type="file"  style="display:none"/>&nbsp;
			<a href="javascript://" id="truncate" title="Click Truncate ALL segment and variables - Be carefull" class="red_btn">Erase Map</a>
		</div>
	</div>	

<!--Legends BOF-->    
<div class='legend_box'>
		<table width="100%" border="0">
				<tr>
						<td>
								<h5>Legends:</h5>
								<font color="dimgray">Item Approved</font><br>
								<font color="red">Item Uknown</font><br>
								<font color="lime">Selected Item for Edit</font><br>
								<h5>Segments view:</h5>	
									<br>static_segment{<font color="lime">5</font>,<font color="red">BF</font>}
								-
								{<font color="lime">Number nea segments</font>} - show count of variables connected to the segment.
								&nbsp;<font color="red">BF</font> - BruteForce mode enabled
								<br><br>Auto Rule: (<font color="lime">l</font><font color="red">d</font><font color="blue">-_</font>)[<font color="maroon">20</font>]<font color="lightblue">.php</font>	
									(<font color="lime">l</font> - is contains letters "a-z",
									<font color="red">d</font>  - contains numbers "0-9",
									<font color="blue">-_</font> - contains characters that not numbers and not letters
									)
									<br>[<font color="maroon">20</font>] - size of "contains" part
									<br><font color="lightblue">.php</font> - static part after rule
						</td>
						<td>
								<h5>Usage:</h5>
				 Mouse Right-Click for rootate tool:<br>
										<table width="100%" style="color:dimgray">
											<tr>
												<td><img  width="20" src="assets/imgs/green.png"></td>
												<td>Select</td><td>allows select elements by mouseover</td>
											</tr>
											<tr>
												<td><img  width="20" src="assets/imgs/red.png"></td>
												<td>Unselect</td>
												<td>allows unselect elements by mouseover</td>
											</tr>
										</table>	 
						</td>
						<td><h5>Mouse Events:</h5>
								<b>Mouseover</b> - show segment info. If Select\Unselect tools enabled - also mark items for edit.<br>
								<b>DoubleClick</b> on segment - if contains variables  - Open Variables List, another open Segments Menu<br>
								<b>DoubleClick</b> on empty space - open Segments\Variables Form with selected items.<br>	
								<b>Drag'n'Drop</b> segment - possible change position of element<br>		
								<b>RightClick</b> switch tools circulary.
						</td>
						<td><img src="assets/imgs/x.png" class="x" id="close_legends"></td>
				</tr>
		</table>
    
</div>    
<!--Legends EOF-->    
    
<!--SEGMENT MULTY MENU BOF--> 
<div id="segment_menu">
	<h5>Edit rule for selected segments<img src="assets/imgs/x.png" id="close_segment_form" class="x" style="float:right;"></h5>
	<div>
        <label class="ccc">Use:</label>&nbsp;
        <label for="use0">Original Path</label>
        <input type="radio" name="use" value="0" id="use0" class="use">&nbsp;&nbsp;
        <label for="use1">Auto Type</label>
        <input type="radio" name="use" value="1" id="use1" class="use">
    </div>
    <input type="hidden" id="segment_menu_ids"><hr> 
    <div class="value_options"></div>
    <div class='type_options'>
		 <label class="ccc">Size:</label>
		 <input type="text" name="size" class="size" placeholder="unlimited" size="3">&nbsp;
		 <label class="ccc">Contains:</label>
		 <input type="checkbox" name="l" class="contains" id="contains_l"><label for="contains_l">Letters</label>
		 <input type="checkbox" name="d" class="contains" id="contains_d"><label for="contains_d">Digital</label>
		 <input type="text" name="s" class="contains" id="contains_s" placeholder="Input special chars" size="17">  
    </div>  
	<div class="type_options">
			<label class="ccc">Have Static part:</label>
			<input type="text"  id="static_part_before" placeholder="Before">
			<input type="text"  id="static_part_after" placeholder="After">
					
	</div>
    <hr>
	<div>
		<input type='checkbox' name='approved' id='approved' checked="checked">	
		<label for="approved">Approved</label>
		&nbsp;
		<input type='checkbox' name='bf' id='bf'>		
		<label for="bf">BF</label>
		&nbsp;
		&nbsp;
		<input type="button" value="save" id="save_codes" class="green_btn">
		
		<input type="button" value="delete" id="delete_segments" class="red_btn">
	</div>
</div>
<!--SEGMENT MULTY MENU EOF--> 
   
<!--VARS SINGLE MENU BOF-->    
<div id="vars_menu"> 
	<h5>Select variable for edit rule<img src="assets/imgs/x.png" id="vars_menu_close" class="x" style="float:right;"></h5>
    <div class="var_request_box"><ul id="requests"></ul></div>    
    <!--div class="vars_tools">
        <img src="assets/imgs/green.png" width="25" id="pencil_var">
        <img src="assets/imgs/red.png" width="25" id="eraser_var">
        <img src="assets/imgs/edit.png" width="25" id="edit_form_var" title="Click For Edit Selected">
    </div-->
     
    <div class="vars_form">
		<h5>Edit rule for selected variables<img id="vars_close_form" src="assets/imgs/x.png" class="x" style="float:right"></h5>
        <input type="hidden" id="vars_menu_ids">
		<div class="vars_value_options"></div>		
       
        
    <div class='vars_type_options'>
     <hr>   
    
	<div class="vars_row3">
		<span class="var_contains_box">
			<label class="ccc">Size:</label>
			<input type="text" name="vars_size" class="vars_size" placeholder="unlimited" size="3">&nbsp;
			<label class="ccc">Contains:</label>
			<input type="checkbox" name="vars_l" class="vars_contains" id="vars_contains_l">
			<label for="vars_contains_l">Letters</label>
			<input type="checkbox" name="vars_d" class="vars_contains" id="vars_contains_d">
			<label for="vars_contains_d">Digital</label>
			<input type="text" name="vars_s" class="vars_contains" id="vars_contains_s" placeholder="Input special chars" size="10">
		</span>	
		<span style="float:right;">
			<label class="ccc" for="exception">Exception</label>
			<input type="checkbox" name="exception" id="exception">
		</span>		
	</div>

    </div>
    <div>
        <div>
            <hr>
            <label for="vars_approved">Approved</label>
            <input type='checkbox' name='vars_approved' id='vars_approved' checked="checked">&nbsp;    
			<label for="vars_global">Make Global</label>
            <input type='checkbox' name='vars_global' id='vars_global'>&nbsp;	
            <input type="button" value="save" id="vars_save_code" class="green_btn">
            <input type="button" value="delete" id="vars_delete_code" class="red_btn">
           
		</div>
     </div>
    </div>
</div>  
<div id="loader"><img src="assets/imgs/loader.gif"></div>
<!--VARS SINGLE MENU EOF-->   
<script>
$(document).ready(function (){
	//start interface
	
	<?php if($WR->isEditor()):?>
	 WaF.init();	
	<?php else:?> 
	 WaF.draw_connect_lines();
	<?php endif;?>
	
});

</script>
</body>
</html>