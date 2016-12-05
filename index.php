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
$from_date=isset($_GET['from_date'])?$_GET['from_date']:date('d-m-Y',strtotime("-30 days"));
$to_date=isset($_GET['to_date'])?$_GET['to_date']:date("d-m-Y",strtotime("-1 day"));
$reqs=$WR->get_dashboard_info($from_date,$to_date);
#$is_mobile=true;
#pre($is_mobile);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php require_once "include/head.php"; ?>
<?php if(!$is_mobile):?>
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
<?php endif;?>
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
					<div id="segments" style="width: 400px; height: 200px;"><?php if($is_mobile):?><img src="//chart.googleapis.com/chart?cht=p&chd=t:<?php echo $segments[1];?>,<?php echo $segments[0];?>&chs=400x150&chl=Approved|Unknown&chco=0000FF,FF0000&chtt=<?php echo array_sum($segments).' Segments recorded for all time:';?>"><?php endif;?></div>
				</td>
				<td align="right">
						<div id="vars" style="width: 400px; height: 200px;"><?php if($is_mobile):?><img src="//chart.googleapis.com/chart?cht=p&chd=t:<?php echo $vars[1];?>,<?php echo $vars[0];?>&chs=400x150&chl=Approved|Unknown&chco=0000FF,FF0000&chtt=<?php echo array_sum($vars).' Variables recorded for all time:';?>"><?php endif;?></div>
				</td>
		</tr>
		<tr>
				<td colspan="2">
					<div id="logs" style="width: 850px; height: 200px;"><?php if($is_mobile):?><img src="//chart.googleapis.com/chart?chs=830x200&cht=ls&chco=0077CC&chd=t:<?php echo implode(',',$reqs['logs'])?>&chtt=<?php echo array_sum($reqs['logs']);?> Attacks Blocked in last <?php echo count($reqs['logs']);?> days:&chxt=x,y&chxr=0,0,<?php echo count($reqs['logs']);?>,1|1,0,<?php echo max($reqs['logs']);?>,10"><?php endif;?></div>
				</td>
		</tr>
	    <tr>
				<td colspan="2">
                        <?php
                        $urls=Array();$nums=Array();
                        
                        foreach($reqs['logs_type'] as $lu)
                        {
                            $types[]=$lu['type']."   (".$lu['num'].")";
                            $type_nums[]=$lu['num'];
                        }
                        
                        ?>
						<div id="logs_type_pie"><?php if($is_mobile):?><img src="//chart.googleapis.com/chart?cht=p&chd=s:Uf9a&chs=800x220&chd=t:<?php echo implode(',',$type_nums)?>&chl=<?php echo implode('|',$types);?>&chdl=<?php echo implode('|',$types);?>&chco=fb0000,00fb1d,f9fb00,9300fb,00fbdb,1500fb&chtt=Attacks by type"><?php endif;?></div>
				</td>		
		</tr>
		<tr>
				<td colspan="2">
                        <?php
                        $urls=Array();$nums=Array();
                        foreach($reqs['logs_url'] as $lu)
                        {
                            $urls[]=$lu['url']."   (".$lu['num'].")";;
                            $nums[]=$lu['num'];
                        }
                        
                        ?>
						<div id="logs_url_pie"><?php if($is_mobile):?><img src="//chart.googleapis.com/chart?cht=p&chd=s:Uf9a&chs=800x220&chd=t:<?php echo implode(',',$nums)?>&chdl=<?php echo implode('|',$urls)?>&chco=fb0000,00fb1d,f9fb00,9300fb,00fbdb,1500fb&chtt=Top 15 attacked scripts in last 30 days"><?php endif;?></div>
				</td>		
		</tr>
		
</table>		
<script>
function load_logs(json_str){
    var logs=JSON.parse(json_str); 
    var dlogs=[['', '']];
    var attacks_sum=0;
    var days=0;
    for(l in logs)
    {
        dlogs.push([new Date(l),logs[l]]);
        attacks_sum+=parseInt(logs[l]);
        days++;
    }
    var data = google.visualization.arrayToDataTable(dlogs);

        var options = {
          title: attacks_sum+' Attacks Blocked in last '+days+' days:',
          hAxis: { format: 'dd/MM', titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('logs'));
        chart.draw(data, options);
}
function load_logs_url(json_str){
    var logs=JSON.parse(json_str); 
    var dlogs=[['', '']];
    for(l in logs)
    {
        var url=(logs[l].url.length>0)?logs[l].url:'/';
        url+='\t'+'['+parseInt(logs[l].num)+']';
         dlogs.push([url,parseInt(logs[l].num)]);
    }
   
    var data = google.visualization.arrayToDataTable(dlogs);
   
    var chart = new google.visualization.PieChart(document.getElementById('logs_url_pie'));
    chart.draw(data, {title: 'Top 15 attacked scripts in last 30 days'});
}

function load_logs_type(json_str){
    //load logs_url_pie
        var logs=JSON.parse(json_str); 
    var dlogs=[['', '']];
    for(l in logs)
    {
     
         dlogs.push([logs[l].type,parseInt(logs[l].num)]);
    }
   
    var data = google.visualization.arrayToDataTable(dlogs);
   
    var chart = new google.visualization.PieChart(document.getElementById('logs_type_pie'));
    chart.draw(data, {title: 'Attacks by type',pieSliceText: 'label'});
}
   $(function() {
       
      $("#from_date" ).datepicker({'dateFormat':'dd-mm-yy',maxDate:0});
      $("#to_date" ).datepicker({'dateFormat':'dd-mm-yy',maxDate:0});
      google.charts.setOnLoadCallback(function (){
          load_logs('<?php echo json_encode($reqs['logs']);?>');  
          load_logs_url('<?php echo json_encode($reqs['logs_url']);?>');
          load_logs_type('<?php echo json_encode($reqs['logs_type']);?>');
      });
	 });
</script>		
</body>
</html>