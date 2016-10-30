/*
 * script for dashboard UI backend
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
var WD={};
WD.init=function (){
  WD.init_dates();  
  WD.load_data();
};
WD.init_dates=function (){
      $("#from_date" ).datepicker({'dateFormat':'dd-mm-yy',maxDate:0});
      $("#to_date" ).datepicker({'dateFormat':'dd-mm-yy',maxDate:0});
};
WD.load_data=function (){
  $.get( "ajax.php?act=dashboard_info&from_date="+$("#from_date" ).val()+"&to_date="+$("#to_date" ).val(), function( json ) {
      
        
         WD.load_logs(json.logs);  
         WD.load_logs_url(json.logs_url);
		 WD.load_logs_type(json.logs_type);
  },'json');  
};

WD.load_logs=function (logs){
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
};
WD.load_logs_url=function (logs){
    //load logs_url_pie
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
};

WD.load_logs_type=function (logs){
    //load logs_url_pie
    var dlogs=[['', '']];
    for(l in logs)
    {
     
         dlogs.push([logs[l].type,parseInt(logs[l].num)]);
    }
   
    var data = google.visualization.arrayToDataTable(dlogs);
   
    var chart = new google.visualization.PieChart(document.getElementById('logs_type_pie'));
    chart.draw(data, {title: 'Attacks by type',pieSliceText: 'label'});
};