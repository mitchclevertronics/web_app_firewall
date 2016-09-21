/* 
 * MAP UI - WebAppFirewall
 * This product includes PHP software, freely available from <http://www.php.net/software/>
 * Author: Roman Shneer romanshneer@gmail.com
 * 21.09.2016
 */
var WaF={};
WaF.code1string='s'; 
WaF.code1number='n'; 

WaF.code2int='i';
WaF.code2float='f';

WaF.code2letter='l';
WaF.code2digital='d';
WaF.code2special='s';
WaF.filter_on=false;
WaF.current_tool='hand';
WaF.tools=['hand','pencil','eraser'];
WaF.init=function (){
	//WaF.init_ul_events();	
        WaF.init_truncate();
        WaF.init_tools();
        WaF.init_li_over();
        WaF.init_tooltip();
        WaF.init_segment_menu();
       // WaF.init_reset_chkboxes();
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
     
  }, cursor: "grabbing",
  stop:function( event, ui ) {
      if(WaF.filter_on==false)
      {
      $(event.target).attr('orig_left',$(event.target).css('left'));
      $(event.target).attr('orig_top',$(event.target).css('top'));
      
      var data={'x':$(event.target).css('left'),
                'y':$(event.target).css('top'),
                'id':$(event.target).attr('segment_id')
                };
        $('body').css('cursor','');        
        $.post( "ajax.php?act=save_segment_position",data, function( json ) {});
      }
  }
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
        
    });
};

/* Event for mouseover action on segment - show segment info*/
WaF.init_tooltip=function()
{
  $(document).tooltip({
    items:'.have_vars',
    tooltipClass:'preview-tip',
    position: { my: "left+15 top", at: "right center" },
    'delay':0,
    show:null,
    open:function (event,ui){
       if (typeof(event.originalEvent) === 'undefined')
        {
            return false;
        }

        var $id = $(ui.tooltip).attr('id');
        $('div.ui-tooltip').not('#' + $id).remove();
     
    },
    hide:null,
    content:function(callback) {
      
       if($(this).attr('popup')==null)
       {
        
       var id=$(this).attr('segment_id');
        $.get('ajax.php?act=segment_info&id='+id, {
            //id:id
        }, function(data) {
            $('.segment'+id).attr('popup',data);
            callback(data); //**call the callback function to return the value**
        });
        }else{
           callback($(this).attr('popup')); 
        }
    },
}); 

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
        $.get( "ajax.php?act=show_segments&ids="+ids.join(','), function( json ) {
            
         WaF.load_segments_form(json);
		
	},'json');
};
/* Init SegmentForm Opening */
WaF.init_segments_form=function (){
    $('#edit_form').click(WaF.open_segments_form);
    $('#seg_tree').dblclick(WaF.open_segments_form);
};



WaF.init_open_filter_form=function (){
 
    $('.filter_fieldset input').change(function (){
        WaF.filter_form();
    });
    
    if($('#filter_segment_id').val().length>0)WaF.filter_form();
    
    $('#filter_help').click(function (){
        $('.legend_box').show();
    });
    $('#close_legends').click(function (){
        $('.legend_box').hide();
    });
};
WaF.filter_form=function (){
    var filter={'approved0':$('#filter_approved0').prop('checked'),
                'approved1':$('#filter_approved1').prop('checked'),
                'bf0':$('#filter_bf0').prop('checked'),
                'bf1':$('#filter_bf1').prop('checked'),
                'use_type0':$('#filter_use_type0').prop('checked'),
                'use_type1':$('#filter_use_type1').prop('checked'),
                'vars0':$('#filter_vars0').prop('checked'),
                'vars1':$('#filter_vars1').prop('checked'),
                'vars_approved0':$('#filter_vars_approved0').prop('checked'),
                'vars_approved1':$('#filter_vars_approved1').prop('checked'),
                'segment_id':$('#filter_segment_id').val()
            };
     var fl=false;
     for(f in filter)
     {
         if((f=='segment_id')&&(filter[f].length))fl=true;
         else if(filter[f]==false)fl=true;
     }
     if(fl)WaF.filter_on=true;
     else WaF.filter_on=false;
     
     
     console.log(WaF.filter_on);
    $('.segment').each(function (s,segment){
        var visible=true;
      
        if($(segment).hasClass('approved1'))
        {
            if(filter.approved1==false)
                visible=false;
        }else{
            if(filter.approved0==false)
                visible=false;
        }
        if($(segment).hasClass('bf1'))
        {
            if(filter.bf1==false)
                visible=false;
        }else{
            if(filter.bf0==false)
                visible=false;
        }
        if($(segment).hasClass('use_type1'))
        {
            if(filter.use_type1==false)
                visible=false;
        }else{
            if(filter.use_type0==false)
                visible=false;
        }
        if($(segment).hasClass('have_vars'))
        {
            if(filter.vars1==false)
                visible=false;
        }else{
           if(filter.vars0==false)
                visible=false; 
        }
        if(($(segment).hasClass('vars_approved1'))||($(segment).hasClass('vars_approved0')))
        {
        if(($(segment).hasClass('vars_approved1'))&&(!filter.vars_approved1))
            visible=false;
        
        if(($(segment).hasClass('vars_approved0'))&&(!filter.vars_approved0))
            visible=false;
        
        }else{
            if((!filter.vars_approved1)||(!filter.vars_approved0))
                visible=false;
        }        
        if((filter.segment_id.length>0)&&($(segment).attr('segment_id')!=filter.segment_id))
        {    
            visible=false;
        }
        if(WaF.filter_on==true)
        {
            if($(segment).attr('orig_left')==null)
                $(segment).attr('orig_left',$(segment).css('left'));
            $(segment).css('left',0);
            if($(segment).attr('orig_top')==null)
                $(segment).attr('orig_top',$(segment).css('top'));
            $(segment).css('top',0);
        }else{
            $(segment).css('left',$(segment).attr('orig_left'));
            $(segment).css('top',$(segment).attr('orig_top'));
        }
        //$(segment).css('left','0');
        //$(segment).css('top','0');
        if(visible){
            $(segment).css('opacity',1);
            $(segment).parent().removeClass('hidden');
        }else{ 
            $(segment).css('opacity',0.3);
            $(segment).parent().addClass('hidden');
        }
          
    });
    
    //algoritm rebuilding
    WaF.rebuild_tree($('#seg_tree ul'),1);
   WaF.redraw_connect_lines();

};
WaF.rebuild_tree=function (obj,lvl){
  var have_visible=false;   
  obj.find('li[lvl='+lvl+']').each(function (i,li){
    
   if($(li).find('ul').html()==null)
   {
       if($(li).hasClass('hidden'))
        {
            //$(li).css('background','pink');    
            $(li).hide();
        }else{
            $(li).show(); 
            have_visible=true;
        }
       
   }else{
       var hv=WaF.rebuild_tree($(li).find('ul'),(lvl+1));
       if(hv==false)
       {
            if($(li).hasClass('hidden')) 
                $(li).hide();
            else $(li).show();
       }else{
           $(li).show();
           have_visible=true;
       }
   }
  });  
  return have_visible;
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
        $(event.target).append($('<a>').html('logs').addClass('log_link').attr('href','logs.php?sid='+$(event.target).attr('segment_id')));
    });
    $('#seg_tree .segment').mouseleave(function (event){
       $(event.target).find('.log_link').remove(); 
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
    $('#seg_tree').removeClass().addClass('body_'+next_tool);
    $('#vars_menu').removeClass().addClass('body_'+next_tool);
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
        
	//$('#segment_menu .data_type').change(function (event){WaF.change_data_type(event);WaF.update_codes();});
	$('#segment_menu .size').change(function (event){WaF.update_codes();});
	$('#segment_menu .contains').change(function (event){WaF.update_codes();});
	$('#segment_menu .number_type').change(function (event){WaF.update_codes();});
	$('#segment_menu #save_codes').click(function (){WaF.save_segments();});
	//$('#delete_code').click(function (){WaF.delete_code();});
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
        //WaF.analize4form();
	return false;
      
      //WaF.open_var_menu($(event.target).parent());
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
                            //var var_text=(json.vars[method][v].use_type==0)?json.vars[method][v].name+'='+json.vars[method][v].value:json.vars[method][v].name+'='+json.vars[method][v].code_contains+" "+json.vars[method][v].code_size;
                            var span=$('<span>').html(json.vars[method][v].name)
                                    .addClass('var_li');
                            var span2=$('<span>').html((json.vars[method][v].use_type==0)?json.vars[method][v].value:json.vars[method][v].code_contains+" "+json.vars[method][v].code_size)
                                    .addClass('var_li_val');
                                                //.append($('<font>').html((json.vars[method][v].use_type==0)?json.vars[method][v].value:json.vars[method][v].code_contains+" "+json.vars[method][v].code_size))
                                                 //;
                            var var_li=$('<li>').append(span).append(span2)
                                                .addClass('approved'+json.vars[method][v].approved)
                                                .attr('rel',json.vars[method][v].id);
                            ul.append(var_li);
                            
                        }
                        
                        
                    
                   
                    li.append(ul);
                   
                    $('#requests').append(li);
			//requests+=json.requests[j].method+' '+json.requests[j].path+"\n";
		}
		WaF.init_li_over_var();
	}
            
            
	},'json');
	
		
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
// console.log(code);
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
/*
WaF.merge_codes=function(codes){
    var COT={'part_before':false,
            'part_after':false,
            'data_type':'number',
            'number_type':'int',
             size:0,
            'contains':{'l':false,'s':'','d':false}};
    var max_string_size=0;
    for(c in codes) 
    {
        var CO=WaF.code_parse(codes[c]);
        if(CO.data_type=='string')
        {
            COT.data_type='string';
         
            if(CO.size)max_string_size=Math.max(max_string_size,parseInt(CO.size));
            if(CO.contains.l==true)COT.contains.l=true;
            if(CO.contains.d==true)COT.contains.d=true;
            
            if(CO.contains.s.length>0)COT.contains.s+=CO.contains.s;
        }else{
            COT.size=Math.max(COT.size,CO.size);
            COT.contains.d=true;
            if(CO.number_type!='int')COT.number_type=CO.number_type;
        }
        
       
    }
  
    if(COT.data_type=='string'){
       
        COT.size=max_string_size;
       if(COT.contains.s.length>0)
       {
           COT.contains.s=WaF.arrayUnique(COT.contains.s.split("")).join("");
           console.log(COT.contains.s);
       }
        delete COT.number_type;
    }
   
    return COT;
};
*/
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
		console.log(json[j].code_size);

        var sdiv=$('<div>').addClass('var').attr('rel',json[j].id)
                .append($('<span>').html(json[j].name))
               .append($('<span>').html('='))

                .append($('<span>').html(json[j].value));
        $('.vars_value_options').append(sdiv);
        if(json[j].approved)approved=1;
        //$('.segments').append(sdiv);
    }
     var contains_str=WaF.arrayUnique(contains.join('').split('')).join('');
    WaF.vars_code2form(contains_str,max_size);
    //var code=WaF.CodeObj2CodeString(CodeObj);
    // $('#var_code').val(code);
     
     
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
        'use':1,
        'code_contains':contains,
        'code_size':$('.vars_size').val()
    };
    
	$.post( "ajax.php?act=vars_save",data, function( json ) {
            var segment_id=$('.opened_segment').parent().attr('id');
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

/*
WaF.delete_code=function (){

var id=$('#rightclk_menu').attr('rel');
	$.get( "ajax.php?act=delete_code&id="+id, function( json ) {
		
		$('.opened_li').remove();
		//$('.opened_li').removeClass('opened_li');
		$("#rightclk_menu").hide();	
	});
	
};
*/

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
/* Action for change SegmentForm field DataType */
/*
WaF.change_data_type=function(event){
	
    if($(event.target).attr('id')=='data_type_string')
    {
    //menu for string
    $('#segment_menu .row3').show();
    $('#segment_menu .row5').hide();								
    }else{
    //menu for int	
    $('#number_type_i').prop('checked',true);
    $('#segment_menu .row3').hide();
    $('#segment_menu .row5').show();
    }
};
*/
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
    //if($('#vars_contains_s').prop('checked')==true)attr.push(WaF.code2special);	
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
    //if($('#segment_menu #contains_s').prop('checked')==true)attr.push(WaF.code2special);	
    code+=':'+contains;	
    }

    code+=':'+$('.size').val();
    code='['+code+']';
   // console.log(code);
    if($('#segment_menu #static_part_before').val().length>0)code=$('#segment_menu #static_part_before').val()+code;
    if($('#segment_menu #static_part_after').val().length>0)code=code+$('#segment_menu #static_part_after').val();
    $('#segment_menu #code').val(code);
};	

