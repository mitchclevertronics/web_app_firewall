<?php
/* 
 * MAP of requests(white-list) 
 * This product includes PHP software, freely available from <http://www.php.net/software/>
 * Author: Roman Shneer romanshneer@gmail.com
 */
session_start();

require_once "libs/db.inc.php";

require_once "libs/waf_report.class.php";

$WR=new WafReport;
$segments=$WR->get_segments_tree(0);
#$reqs=$segments['childs'];

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
<script src="assets/js/jquery.connections.js"></script>		
<script src="assets/js/waf_map.js"></script>
</head>
<body>
<?php include_once 'include/header.php';?>           
<?php if($segments):?> 
<div class="tree_house">
		
    <div id="seg_tree">    
     <?php echo $WR->draw_segments_tree($segments,1); ?>
    </div>
    <div id="tools_panel">
            <img src="assets/imgs/pencil.png" width="40" id="pencil">
            <img src="assets/imgs/eraser.png" width="40" id="eraser">
            <img src="assets/imgs/edit.png" width="40" id="edit_form" title="Click For Edit Selected">
            <img src='assets/imgs/roger.png' width="40" id="truncate" title="Click Truncate ALL data - Be carefull">  
              
    </div>    
</div>    
<?php endif;?>  
<div class='filter_box'>
		<strong>Show Segments only:</strong>
		<fieldset class='filter_fieldset'>
		 <div class='filter_fieldset_header'>Status</div>		
		 <input type='checkbox' id='filter_approved1' checked='checked'><label for='filter_approved0'>Approved</label>&nbsp;
		 <input type='checkbox' id='filter_approved0' checked='checked'><label for='filter_approved1'>Unknow</label>&nbsp;
		</fieldset>		
		<fieldset class='filter_fieldset'>
		 <div class='filter_fieldset_header'>BF</div>		
		 <input type='checkbox' id='filter_bf1' checked='checked'><label for='filter_bf1'>On</label>&nbsp;
		 <input type='checkbox' id='filter_bf0' checked='checked'><label for='filter_bf0'>Off</label>&nbsp;
		</fieldset>
		<fieldset class='filter_fieldset'>
		<div class='filter_fieldset_header'>Use Rule:</div>		
		<input type='checkbox' id='filter_use_type0' checked='checked'>
		<label for='filter_use_type0' title='Static - checking via entered value'>Static</label>&nbsp;		
		<input type='checkbox' id='filter_use_type1' checked='checked'>
				<label for='filter_use_type1' title='AutoType, Code used for filter entering data'>Rule</label>&nbsp;		
		</fieldset>				
		<fieldset class='filter_fieldset'>
		<div class='filter_fieldset_header'>Have Vars:</div>		
		<input type='checkbox' id='filter_vars0' checked='checked'><label for='filter_vars0'>No</label>&nbsp;				
		<input type='checkbox' id='filter_vars1' checked='checked'><label for='filter_vars1'>Have</label>&nbsp;				
		</fieldset>						
		<fieldset class='filter_fieldset'>
		<div class='filter_fieldset_header'>Vars Status:</div>		
		<input type='checkbox' id='filter_vars_approved1' checked='checked'><label for='filter_vars_approved1'>Approved</label>&nbsp;				
		<input type='checkbox' id='filter_vars_approved0' checked='checked'><label for='filter_vars_approved0'>Unknow</label>&nbsp;				
		</fieldset>
		<fieldset class='filter_fieldset'>
				<input type="text" placeholder="Segment ID" id="filter_segment_id" style="width:80px;" value="<?php echo isset($_GET['sid'])?$_GET['sid']:'';?>">		
		</fieldset>
		<img src='assets/imgs/question.png' width="20" id="filter_help" title="Help">	
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
								Number nea segments - show count of variables
						</td>
						<td>
								<h5>Usage:</h5>
								
                 Point cursor on Tools in right part of the screen:<br>
										<table width="100%" style="color:dimgray">
												<tr><td><img  width="20" src="assets/imgs/pencil.png"></td>
														<td>Pensil Tool</td><td>allows select elements by mouseover</td></tr>
												<tr><td><img  width="20" src="assets/imgs/eraser.png"></td>
														<td>Eraser Tool</td>
														<td>allows unselect elements by mouseover</td></tr>
												<tr><td><img  width="20" src="assets/imgs/edit.png"></td>
														<td>Edit Tool</td>
														<td>open Segment form for selected elements</td></tr>
										</table>	 
						</td>
						<td><h5>Mouse Events:</h5>
								Mouseover - show segment info<br>
                DoubleClick on segment - Open Variables List<br>
								Drag'n'Drop segment - possible change position of element<br>		
								RightClick switch tools circulary.<br>
								DoubleClick on empty space - open Segments Form with selected items.
										</td>
						<td><input type="button" value="Close" id="close_legends" class="add_user"></td>
				</tr>
		</table>
    
</div>    
<!--Legends EOF-->    
    
<!--SEGMENT MULTY MENU BOF--> 
<div id="segment_menu">
    <input type="hidden" id="segment_menu_ids"> 
    <div class="value_options"></div>
    <div >
        <b>Use:</b>&nbsp;
        <label for="use0">Original Path</label>
        <input type="radio" name="use" value="0" id="use0" class="use">&nbsp;&nbsp;
        <label for="use1">Auto Type</label>
        <input type="radio" name="use" value="1" id="use1" class="use">
    </div>
		<div class="type_options">
				<hr>
				<input type="text"  id="static_part_before" placeholder="No static part Before">
				<input type="text"  id="static_part_after" placeholder="No static part After">
				<label>Size:</label>
				<input type="text" name="size" class="size" placeholder="unlimited">		
		</div>
    <div class='type_options'>
		 <label>Contains:</label>
		 <input type="checkbox" name="l" class="contains" id="contains_l"><label for="contains_l">Letters</label>
		 <input type="checkbox" name="d" class="contains" id="contains_d"><label for="contains_d">Digital</label>
		 <input type="text" name="s" class="contains" id="contains_s" placeholder="Input special chars">  
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
		<input type="button" value="save" id="save_codes">
		<input type="button" value="close" id="close_segment_form">
    <input type="button" value="delete" id="delete_segments">
	</div>
</div>
<!--SEGMENT MULTY MENU EOF--> 
   
<!--VARS SINGLE MENU BOF-->    
<div id="vars_menu"> 
    <div style="text-align: right;">
    <img src="assets/imgs/question.png" width="20" title="Point cursor on Tools in right part of the window:
            Pensil Tool - allows select variables by mouseover
            Eraser Tool - allows unselect variables by mouseover
            Edit Tool - open Variable form for selected variables
            " class="tooltip">
    </div>
    <div class="var_request_box">
    <ul id="requests">
    </ul>
    </div>    
    <div class="vars_tools">
        <img src="assets/imgs/pencil.png" width="25" id="pencil_var">
        <img src="assets/imgs/eraser.png" width="25" id="eraser_var">
        <img src="assets/imgs/edit.png" width="25" id="edit_form_var" title="Click For Edit Selected">
        <span style="margin:2px;" id="vars_menu_close">close</span>    
    </div>
     
    <div class="vars_form">
        <input type="hidden" id="vars_menu_ids">
				<div class="vars_value_options"></div>		
       
        
    <div class='vars_type_options'>
     <hr>   
       
	<div class="vars_row2">
		<label>Size:</label>
		<input type="text" name="vars_size" class="vars_size" placeholder="unlimited">
	</div>
	<div class="vars_row3">
		<label>Contains:</label>
		<input type="checkbox" name="vars_l" class="vars_contains" id="vars_contains_l">
                    <label for="vars_contains_l">Letters</label>
		<input type="checkbox" name="vars_d" class="vars_contains" id="vars_contains_d">
                    <label for="vars_contains_d">Digital</label>
                <input type="text" name="vars_s" class="vars_contains" id="vars_contains_s" placeholder="Input special chars">      
		<!--input type="checkbox" name="vars_s" class="vars_contains" id="vars_contains_s">
                    <label for="vars_contains_s">Special</label-->
	</div>

    </div>
    <div>
        <div>
            <hr>
            <label for="vars_approved">Approved</label>
            <input type='checkbox' name='vars_approved' id='vars_approved' checked="checked">&nbsp;    
            <input type="button" value="save" id="vars_save_code">
            <input type="button" value="delete" id="vars_delete_code">
            <input type="button" value="close" id="vars_close_form">    
	</div>
     </div>
    </div>
</div>  
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