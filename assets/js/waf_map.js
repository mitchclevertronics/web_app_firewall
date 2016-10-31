/*
 * script for WAF report JS UI backend
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
var WaF={};
WaF.code1string='s'; 
WaF.code1number='n'; 

WaF.code2int='i';
WaF.code2float='f';

WaF.code2letter='l';
WaF.code2digital='d';
WaF.filter_on=false;
WaF.current_tool='hand';
WaF.tools=['hand','pencil','eraser'];
WaF.opened_segment=0;
WaF.init=function (){
	
        WaF.init_truncate();
        WaF.init_tools();
        WaF.init_li_over();
        WaF.init_tooltip();
        WaF.init_segment_menu();
       
        WaF.init_segments_form();
        WaF.init_open_filter_form();
        WaF.init_close_segment_form();
        WaF.init_delete_vars_form();
        WaF.init_delete_segment_form();
        WaF.init_open_vars_menu();
        WaF.init_open_vars_form();
        WaF.init_vars_menu_close();
        WaF.init_text_btns();
        WaF.draw_connect_lines();
		
};

WaF.draw_connect_lines=function (){
    
//drag n drop
  $('.segment:visible').draggable({drag: function( event, ui ) {
        WaF.redraw_connect_lines();
     
  }, cursor: "grabbing"
  });
  
  //line connection
  $('.segment').each(function (s,segment){
      if($(segment).attr('segment_parent')!=0)
      {
      
       $('.segment'+$(segment).attr('segment_id'))
               .connections({ to: '.segment'+$(segment).attr('segment_parent')});    
        
      }
     
  });
};
WaF.redraw_connect_lines=function (){
 $('connection').connections('update');
 $('#popup').remove();
};

/* Event for show\hide help text buttons */
WaF.init_text_btns=function (){
  $('.usage_text_btn').click(function (){
      if($('#usage_text').is(':hidden'))$('#usage_text').show();
      else $('#usage_text').hide();    
  });
  
  $('.legends_text').click(function (){
      if($('#legends_text').is(':hidden'))$('#legends_text').show();
      else $('#legends_text').hide();    
  });
};

/* Event for closing VariableMenu*/
WaF.init_vars_menu_close=function (){
    $('#vars_menu_close').click(function(){
        $('.opened_segment').removeClass('opened_segment');
        $('.vars_form').hide();
        $('#vars_menu').hide();
        $('#edit_global_vars').removeClass('the_action');
    });
};

/* Event for mouseover action on segment - show segment info*/
WaF.init_tooltip=function()
{
$('html').mousemove(function (e){
	WaF.clientX=e.clientX;
	WaF.clientY=e.clientY;
});
$('.have_vars').mousemove(function(e){
	
	if($(e.target).attr('segment_id'))
	{
		//console.log($(e.target).attr('segment_id'));
		var id=$(e.target).attr('segment_id');
		if((id!=WaF.opened_segment)&&(WaF.opened_segment==0))
		{	
		WaF.opened_segment=id;	
		$(e.target).append($('<a>').html('logs').addClass('log_link').attr('href','logs.php?sid='+id));
        $.get('ajax.php?act=segment_info&id='+id, {
            //id:id
        }, function(data) {
		var popup=$('<div>');
			popup.html(data).attr('id','popup');	
			
			var rect=$(e.target)[0].getBoundingClientRect();
			popup.css('top',(rect.top+(window.scrollY||document.documentElement.scrollTop)));
			popup.css('left',(rect.right+(window.scrollX||document.documentElement.scrollLeft)+5));
			$('body').append(popup);
			WaF.autoClose($(e.target));
			
        });
		}
	}
});	
	
};
WaF.autoClose=function (element){
	if(element!=null)
	{
	var rect=element[0].getBoundingClientRect();
	var result=WaF.cursor_on_item(element[0].getBoundingClientRect());
	
	
		if(result==false)
		{

			$('#popup').remove();
			WaF.opened_segment=0;
			element.find('.log_link').remove(); 
		}else{
			setTimeout(function (){WaF.autoClose(element);},100);
		}
	}
};
//return true if mouse coordinates in given rect
WaF.cursor_on_item=function (rect){
	//console.log(WaF.clientY+':'+rect.top+':'+rect.bottom);
	//console.log(WaF.clientX+':'+rect.left+':'+rect.right);
	var result=(((WaF.clientY>=rect.top)&&(WaF.clientY<=rect.bottom))&&((WaF.clientX>=rect.left)&&(WaF.clientX<=rect.right)))?true:false;
	return result;
};

/* Event for Tools button delete selected segments */
WaF.init_delete_segment_form=function (){
     $('#delete_segments').click(function (){
       if(confirm("Sure delete Segments?"))
       {
        var ids=$('#segment_menu_ids').val();
	$.get( "ajax.php?act=delete_segments&ids="+ids, function( json ) {
           $('#segment_menu').hide();
	   window.location.reload();
	});
        }
            
    });
};

/* Event for Tools button delete selected vars */
WaF.init_delete_vars_form=function (){
     $('#vars_delete_code').click(function (){
       if(confirm("Sure delete Variables?"))
       {
        var ids=$('#vars_menu_ids').val();
           
	$.get( "ajax.php?act=delete_vars&ids="+ids, function( json ) {
           var segment_id=$('.opened_segment').parent().attr('id');
           WaF.open_vars_menu(segment_id);
            $('.vars_form').hide();
	});
       }
            
    });
};

/* Event for button close SegmentMenu */
WaF.init_close_segment_form=function(){
    $('#close_segment_form').click(function (){
        $('#segment_menu').hide();
        $('.selected').removeClass('selected');
    });
    $('#vars_close_form').click(function (){
        $('.vars_form').hide();
        $('.selected_var').removeClass('selected_var');
    });
    
};
/* SegmentForm Opening */
WaF.open_segments_form=function(){
     var ids=[];
        $('.selected').each(function (elid,el){            
            ids.push($(el).parent().attr('id'));
        });
   if(ids.length)$.get( "ajax.php?act=show_segments&ids="+ids.join(','), function( json ) {WaF.load_segments_form(json);},'json');
};

/* Init SegmentForm Opening */
WaF.init_segments_form=function (){
    $('#edit_form').click(WaF.open_segments_form);

	$('.tree_house').dblclick(WaF.open_segments_form);
	$('.tree_house').click(function (){
		if($('#segment_menu').is(":visible"))
		{
			$('#segment_menu').hide();
		}
	});
};



WaF.init_open_filter_form=function (){
    $('#filter_help').click(function (){
        $('.legend_box').show();
    });
    $('#close_legends').click(function (){
        $('.legend_box').hide();
    });
};

WaF.open_vars_form=function (){
    var ids=[];
        $('#requests .selected_var').each(function (elid,el){            
            ids.push($(el).parent().attr('rel'));
        });
        $.get( "ajax.php?act=show_vars&ids="+ids.join(','), function( json ) {
            
         WaF.load_vars_form(json);
		
	},'json');
};
/* Event for opening VariablesForm for selected variables */
WaF.init_open_vars_form=function (){
    $('#edit_form_var').click(WaF.open_vars_form);
    $('#vars_menu').dblclick(WaF.open_vars_form);
	$('.var_request_box').click(function (){if($('.vars_form').is(':visible'))$('.vars_form').hide();});
};

/* Reg event select\unselect Segment via Selected before Tool */
WaF.init_li_over=function (){
    $('#seg_tree .segment').mouseenter(function (event){
        
        switch(WaF.current_tool)
        {
            case 'pencil':
            $(event.target).addClass('selected');    
            break;
            case 'eraser':
            $(event.target).removeClass('selected');    
            break;
        }
       
    });
    
};
/* Reg event select\unselect Segment via variable before VarTool */
WaF.init_li_over_var=function (){
     $('.var_li').mouseenter(function (event){
         var obj=$(event.target).hasClass('var_li')?$(event.target):$(event.target).parent();
         switch(WaF.current_tool)
        {
            case 'pencil':
            obj.addClass('selected_var');    
            break;
            case 'eraser':
            obj.removeClass('selected_var');    
            break;
        }
       
    });
};

/*Events for MouseOver on Tools Elements - segments and variables both*/
WaF.init_tools=function (){
    $('#pencil').mouseover(function (){  
        WaF.switch_tool('pencil');
    });  
    $('#eraser').mouseover(function (){
        WaF.switch_tool('eraser');
    });  
    
    $('#pencil_var').mouseover(function (){  
        WaF.switch_tool('pencil');
    });  
    $('#eraser_var').mouseover(function (){
        WaF.switch_tool('eraser');
    });  
    //right menu event
    document.oncontextmenu = function() {return false;};
  $(document).mousedown(function(e){ 
    if( e.button == 2 ) { 
   
     WaF.switch_tool(WaF.switch2next_tool());
      return false; 
    } 
    return true; 
  }); 
};
WaF.switch2next_tool=function(){
     var next_tool;
    switch(WaF.current_tool)
    {
        case 'hand':
            next_tool='pencil';
        break;
        case 'pencil':
            next_tool='eraser';
        break;
        case 'eraser':
            next_tool='hand';
        break;
    }
    return next_tool;
};
WaF.switch_tool=function (next_tool){

    switch(next_tool)
    {
        case 'pencil':
            $('#pencil').addClass('the_action');
            $('#eraser').removeClass('the_action');
            
            $('#pencil_var').addClass('the_action_var');
            $('#eraser_var').removeClass('the_action_var');
        break;
        case 'eraser':
           
            $('#pencil').removeClass('the_action');
            $('#eraser').addClass('the_action');
            
            $('#eraser_var').addClass('the_action_var');
            $('#pencil_var').removeClass('the_action_var');
            
        break;
        case 'hand':
            $('#pencil').removeClass('the_action');
            $('#eraser').removeClass('the_action');
            $('#eraser_var').removeClass('the_action_var');
            $('#pencil_var').removeClass('the_action_var');
        break;
    }
	$('html').removeClass().addClass('body_'+next_tool);
    WaF.current_tool=next_tool;
};
/* Truncate Btn Event */
WaF.init_truncate=function (){
    $('#truncate').click(function (){
     if(confirm("Are you sure Want DELETE ALL?"))   
     {
        $.get( "ajax.php?act=truncate", function( json ) {
            window.location.reload();
        });
    }   
    });
};

/*Event for changes on Segment form */
WaF.init_segment_menu=function (){
    $('#segment_menu .use').change(function (event){WaF.change_use_type(event);});
    $('#segment_menu .size').change(function (event){WaF.update_codes();});
	$('#segment_menu .contains').change(function (event){WaF.update_codes();});
	$('#segment_menu .number_type').change(function (event){WaF.update_codes();});
	$('#segment_menu #save_codes').click(function (){WaF.save_segments();});
	$('#segment_menu #static_part_before').change(function (event){WaF.update_codes();});
	$('#segment_menu #static_part_after').change(function (event){WaF.update_codes();});
       
};
/*Event for dblclick on segment - open Variables Menu */
WaF.init_open_vars_menu=function (){
  $('.have_vars').dblclick(function (event){
      //unset other opened_li
      $('.opened_segment').removeClass('opened_segment');
      $(event.target).addClass('opened_segment');
		var seg_id=$(event.target).parent().attr('id');
		WaF.open_vars_menu(seg_id);
		return false;
	  });
	  $('#edit_global_vars').click(function (){
		  $('#edit_global_vars').addClass('the_action');
		  WaF.open_vars_menu(0);
	  });
};

WaF.open_vars_menu=function (seg_id){
    
	$.get( "ajax.php?act=show_segment&id="+seg_id, function( json ) {
        $('#vars_menu').show();
          $('#requests').html(null);
	if(typeof(json.vars)!='undefined')
	{
		//var requests='';
		for(method in json.vars)
		{
                   
                    //draw request     
                    var li=$('<li>').html($('<span>').html(method)).attr('rel',method);
                    //draw variables
                    var ul=$('<ul>').addClass('vars_of_'+method);
                    
                        for(v in json.vars[method])
                        {
                           
                            var span=$('<span>').html(json.vars[method][v].name)
                                    .addClass('var_li');
                            var span2=$('<span>').html((json.vars[method][v].use_type==0)?WaF.escapeHtml(json.vars[method][v].value):json.vars[method][v].code_contains+" "+json.vars[method][v].code_size)
                                    .addClass('var_li_val');
                           
                            var var_li=$('<li>').append(span).append(span2)
                                                .addClass('approved'+json.vars[method][v].approved)
                                                .attr('rel',json.vars[method][v].id);
                            ul.append(var_li);
                            
                        }
                        
                        
                    
                   
                    li.append(ul);
                   
                    $('#requests').append(li).attr('segment_id',seg_id);
		
		}
		WaF.init_li_over_var();
		if(seg_id==0)$('#vars_global').prop('disabled',true);
		else $('#vars_global').prop('disabled',false);
			
	}
            
            
	},'json');
	
		
};
WaF.escapeHtml=function (text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
};
WaF.vars_code2form=function(contains,size)
{
   

        $('#vars_contains_l').prop('checked',(contains.indexOf("l")>-1)?true:false);
        
        $('#vars_contains_d').prop('checked',(contains.indexOf("d")>-1)?true:false);
        contains=contains.replace('l','').replace('d','');
        $('#vars_contains_s').val(contains);
    
     $('.vars_size').val(size);
};

/* Event for changes on Variable Form */
WaF.init_vars_menu=function (){ 
	$('#vars_save_code').click(function (){WaF.vars_save();});    
};

/* Translate code string to CodeObj for easy work */
WaF.code_parse=function (code)
{
 var item={};
 var before=code.substr(0,code.indexOf("[")-1);
 
if(before.length>0)item.part_before=before;
else item.part_before=false; 
 var after=code.substr(code.indexOf("]")+1);
 
 if(after.length>0)item.part_after=after;
 else item.part_after=false;
 
 var body=code.substr(code.indexOf("[")+1,code.indexOf("]")-1);
 
 var p=body.split(':');
 
 item.data_type=(p[0]=='s')?'string':'number';
 if(item.data_type=='string')
 {
     item.contains={'l':false,'d':false};
     if(p[1].indexOf('l')>-1)item.contains.l=true;
     //if(p[1].indexOf('s')>-1)item.contains.s=true;
     if(p[1].indexOf('d')>-1)item.contains.d=true;
     var s=p[1].replace('l','').replace('d','');
     item.contains.s=s;
 }else{
     if(p[1]=='f')item.number_type='float';
     else item.number_type='int';
 }
 item.size=p[2];
    
 
 return item;
};

WaF.arrayUnique = function(a) {
    return a.reduce(function(p, c) {
        if (p.indexOf(c) < 0) p.push(c);
        return p;
    }, []);
};
/*Clickeed Edit button for selected variables- load multy form */
WaF.load_vars_form=function (json){
    
    var ids=[];
    var codes=[];
    var approved=0;
    var contains=[];
    var max_size=0;
    $('.vars_value_options').html('');  
    for(j in json)  
    {
        ids.push(json[j].id);
        contains.push(json[j].code_contains);
        max_size=Math.max(max_size,json[j].code_size);
        var sdiv=$('<div>').addClass('var').attr('rel',json[j].id)
                .append($('<span>').html(json[j].name))
               .append($('<span>').html('='))

                .append($('<span>').html(WaF.escapeHtml(json[j].value)));
        $('.vars_value_options').append(sdiv);
        if(json[j].approved)approved=1;
      
    }
    var contains_str=WaF.arrayUnique(contains.join('').split('')).join('');
    WaF.vars_code2form(contains_str,max_size);
    
  $('#vars_menu_ids').val(ids.join(','));
  
  $('#vars_approved').prop('checked',(approved==1)?true:false);  
 
  WaF.init_vars_menu();
  $('.vars_form').show();
  

};

/*Clickeed Edit button for selected segments - load multy form*/
WaF.load_segments_form=function (json){
    var ids=[];
   
    var approved=1;
    var bf=0;
  $('.value_options').html('');  
  var contains=[];
  var max_size=0;
  var code_before=[];
  var code_after=[];
  for(j in json)  
  {
      ids.push(json[j].id);
      max_size=Math.max(max_size,json[j].code_size);
      contains.push(json[j].code_contains)
      if(json[j].approved==0)approved=0;
      if(json[j].code_before.length>0)
          code_before.push(json[j].code_before);
     if(json[j].code_after.length>0) 
      code_after.push(json[j].code_after);
        if(json[j].bf>0)bf=1;
        
      var sdiv=$('<div>').attr('rel',json[j].id)
              .append($('<span>').addClass('lvl').html(json[j].lvl+':'))
              .append($('<span>').html('='))
              
              .append($('<span>')
                        .append($('<label>').html('Value:'))
                        .append($('<input type=text name=val class=val readonly=readonly value='+json[j].value+'>'))
                    );
      $('.value_options').append(sdiv);
     
      //$('.segments').append(sdiv);
  }

  var contains_str=WaF.arrayUnique(contains.join('').split('')).join('');
  var cb=WaF.arrayUnique(code_before);
  
  var ca=WaF.arrayUnique(code_after);
 
  if(json[0].use_type==0)
        {
            $('#use0').prop('checked',true);
            $('.type_options').hide();
            $('.value_options').show();
        }else{
            $('#use1').prop('checked',true);
            $('.type_options').show();
            $('.value_options').hide();
        }
  $('#segment_menu_ids').val(ids.join(','));
  $('#segment_menu').show();
  

            $('#contains_l').prop('checked',(contains_str.indexOf('l')>-1)?true:false);
            $('#contains_d').prop('checked',(contains_str.indexOf('d')>-1)?true:false);
            contains_str=contains_str.replace('l','').replace('d','');
           
            $('#contains_s').val(contains_str);
	
        //set size
	$('.size').val(max_size);
        
        if(typeof(cb[0])!='undefined')
            $('#static_part_before').val(cb[0]);
        if(typeof(ca[0])!='undefined')
            $('#static_part_after').val(ca[0]);
        $('#approved').prop('checked',(approved==1)?true:false);
        $('#bf').prop('checked',(bf==1)?true:false);
};


/* Event for Save Variable Form */
WaF.vars_save=function (){
    var contains='';
    if($('#vars_contains_l').is(':checked'))contains+='l';
    if($('#vars_contains_d').is(':checked'))contains+='d';
    contains+=$('#vars_contains_s').val();
    var data={
        'ids':$('#vars_menu_ids').val(),
        'approved':($('#vars_approved').is(":checked"))?1:0,
		'global':($('#vars_global').is(":checked"))?1:0,
        'use':1,
        'code_contains':contains,
        'code_size':$('.vars_size').val()
    };
    
	$.post( "ajax.php?act=vars_save",data, function( json ) {
            //var segment_id=$('.opened_segment').parent().attr('id');
			var segment_id=$('#requests').attr('segment_id');
			
           WaF.open_vars_menu(segment_id);
            $('.vars_form').hide();
			
	});
};

/*Save SegmentForm*/
WaF.save_segments=function (){
    var contains='';
    if($('#contains_l').is(':checked'))contains+='l';
    if($('#contains_d').is(':checked'))contains+='d';
    contains+=$('#contains_s').val();
    var data={'approved':($('#approved').is(":checked"))?1:0,
              'bf':($('#bf').is(":checked"))?1:0,  
              'ids':$('#segment_menu_ids').val(),
              'use':($('#use0').is(':checked')==true)?0:1,
              'code_contains':contains,
              'code_size':$('.size').val(),
              'code_before':$('#static_part_before').val(),
              'code_after':$('#static_part_after').val()
    };
    
    $.post( "ajax.php?act=save_segments",data, function( json ) {
       $('#segment_menu').hide();
      window.location.reload();
    });
	
};

/* Action for change VariableForm field DataType */
WaF.change_vars_data_type=function (event){
    if($(event.target).attr('id')=='vars_data_type_string')
    {
    //menu for string
    $('.vars_row3').show();
    $('.vars_row5').hide();								
    }else{
    //menu for int	
    $('#vars_number_type_i').prop('checked',true);
    $('.vars_row3').hide();
    $('.vars_row5').show();
    }
};

/* Action for change Autotype\Static field on Segment Form*/
WaF.change_use_type=function (event){
    
    	if($(event.target).attr('id')=='use0')
        {
           $('.type_options').hide();
           $('.value_options').show();
        }else{
           $('.type_options').show(); 
           $('.value_options').hide();
        }
};

/* Get code String from parsing VariablesForm INTO vars_code field */
WaF.vars_update_code=function(){
    var code='';
    if($('.vars_data_type:checked').attr('id')=='vars_data_type_int')
    {
    code+=WaF.code1number;	

        if($('.vars_number_type:checked').attr('id')=='vars_number_type_i')
        {
                code+=':'+WaF.code2int;
        }else{
                code+=':'+WaF.code2float;
        }
    }else{
    code+=WaF.code1string;	
    var contains='';
    if($('#vars_contains_l').prop('checked')==true)contains+=(WaF.code2letter);
    if($('#vars_contains_d').prop('checked')==true)contains+=(WaF.code2digital);
    if($('#vars_contains_s').val().length>0)
    {
        contains+=$('#vars_contains_s').val().replace(':','p');
    }
    code+=':'+contains;	
    }

    code+=':'+$('.vars_size').val();
    code='['+code+']';
   
    $('#var_code').val(code);
};

/* Get code String from parsing SegmentForm INTO #code field */
WaF.update_codes=function (){
   var code='';
    if($('#segment_menu .data_type:checked').attr('id')=='data_type_int')
    {
    code+=WaF.code1number;	

            if($('#segment_menu .number_type:checked').attr('id')=='number_type_i')
            {
                    code+=':'+WaF.code2int;
            }else{
                    code+=':'+WaF.code2float;
            }
    }else{
    code+=WaF.code1string;	
    var contains='';
    if($('#segment_menu #contains_l').prop('checked')==true)contains+=(WaF.code2letter);
    if($('#segment_menu #contains_d').prop('checked')==true)contains+=(WaF.code2digital);
    
    if($('#segment_menu #contains_s').val().length>0)
    {
       contains+=$('#segment_menu #contains_s').val().replace(':','p');
    }
    code+=':'+contains;	
    }

    code+=':'+$('.size').val();
    code='['+code+']';
    if($('#segment_menu #static_part_before').val().length>0)code=$('#segment_menu #static_part_before').val()+code;
    if($('#segment_menu #static_part_after').val().length>0)code=code+$('#segment_menu #static_part_after').val();
    $('#segment_menu #code').val(code);
};	

