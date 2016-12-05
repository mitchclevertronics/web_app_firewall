<?php
/*
 * script for dashboard (backend)
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
require_once "libs/db.inc.php";

require_once "libs/waf_report.class.php";

$WR=new WafReport;
$segments=$WR->get_segments_statistics();
$vars=$WR->get_vars_statistics();
$is_mobile=$WR->ismobile();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php require_once "include/head.php"; ?>
<script type="text/javascript" src="assets/js/waf_dashboard.js"></script>	
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});

google.charts.setOnLoadCallback(drawChart);
function drawChart() {
	//load segments		
	var data = google.visualization.arrayToDataTable([
		['', ''],
		['Approved',     <?php echo $segments[1];?>],
		['Unknown',      <?php echo $segments[0];?>]
	]);
	var chart = new google.visualization.PieChart(document.getElementById('segments'));
	chart.draw(data, {title: '<?php echo array_sum($segments);?> Segments recorded for all time:',pieSliceText: 'label'});
	//load vars
	var data = google.visualization.arrayToDataTable([
		['', ''],
		['Approved',     <?php echo $vars[1];?>],
		['Unknown',      <?php echo $vars[0];?>]
	]);
	var chart = new google.visualization.PieChart(document.getElementById('vars'));
	chart.draw(data, {title: '<?php echo array_sum($vars);?> Variables recorded for all time:',pieSliceText: 'label'});
	
}
</script>
</head>
<body>
<?php include_once 'include/header.php';?>    
<div class="logs_search_form" style="text-align: center;background:#fff;">
				<form action="" method="GET">	
						From Date: <input type="text" id="from_date" name="from_date" size="10" class="inset" value="<?php echo isset($_GET['from_date'])?$_GET['from_date']:date('d-m-Y',strtotime("-30 days"));?>" readonly>
						To Date: <input type="text" id="to_date" name="to_date" size="10" class="inset" value="<?php echo isset($_GET['to_date'])?$_GET['to_date']:date("d-m-Y",strtotime("-1 day"));?>" readonly>
					<input type="submit"	id="search_logs" value="Search">	
				</form>
		</div>		
<table border="0" cellpadding="10" cellspacing="10" id="dashboard_tbl">
		<tr>
				<td align="left">
					<div id="segments" style="width: 400px; height: 200px;"><?php if($is_mobile):?><img src="//chart.googleapis.com/chart?cht=p3&chd=t:<?php echo $segments[1];?>,<?php echo $segments[0];?>&chs=400x150&chl=Approved|Unknown&chco=0000FF,FF0000&chtt=<?php echo array_sum($segments).' Segments recorded for all time:';?>"><?php endif;?></div>
				</td>
				<td align="right">
						<div id="vars" style="width: 400px; height: 200px;"><?php if($is_mobile):?><img src="//chart.googleapis.com/chart?cht=p3&chd=t:<?php echo $vars[1];?>,<?php echo $vars[0];?>&chs=400x150&chl=Approved|Unknown&chco=0000FF,FF0000&chtt=<?php echo array_sum($vars).' Variables recorded for all time:';?>"><?php endif;?></div>
				</td>
		</tr>
		<tr>
				<td colspan="2">
					<div id="logs" style="width: 850px; height: 200px;"></div>
				</td>
		</tr>
	    <tr>
				<td colspan="2">
						<div id="logs_type_pie"></div>
				</td>		
		</tr>
		<tr>
				<td colspan="2">
						<div id="logs_url_pie"></div>
				</td>		
		</tr>
		
</table>		
<script>
   $(function() {
	 google.charts.setOnLoadCallback(WD.init);
	 });
</script>		
</body>
</html>