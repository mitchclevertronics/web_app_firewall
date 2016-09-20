<?php
session_start();
#require_once "libs/config.inc.php";

require_once "libs/db.inc.php";

require_once "libs/waf_report.class.php";


Class WafAjax{
 
 function __construct($act) {
	$this->WR=new WafReport;
	$method_name='get_'.$act;
	if(method_exists($this, $method_name))
	{
	 $this->$method_name();
	}else{
	 $this->get_unknow_method();
	}
 }
 
 private function get_unknow_method(){
	$this->draw_html_result('Unknow action');
 }
 private function draw_html_result($html)
 {
	die($html);
 }
 private function draw_json_result($reqs)
 {
	die(json_encode($reqs));
 }
 private function get_view()
 {
	$reqs=$this->get_next_map($_GET['p']);
	$this->draw_json_result($reqs);
 }
 /*
 private function get_save_segments(){
	 if($this->WR->isEditor())
	 $reqs=$this->WR->save_segments(explode(',',$_GET['ids']),$_GET['code'],$_GET['approved'],$_GET['use']);
	 $this->draw_json_result($reqs);
 }*/
 private function get_save_segments(){
	
	 if($this->WR->isEditor())
	 $reqs=$this->WR->save_segments($_POST);
	 $this->draw_json_result($reqs);
 }
 /*
 private function get_vars_save(){
	if($this->WR->isEditor())
			$reqs=$this->WR->vars_save(explode(',',$_GET['ids']),$_GET['code'],$_GET['approved'],$_GET['use']);
	$this->draw_json_result($reqs);
 }*/
 private function get_vars_save(){
	if($this->WR->isEditor())
			$reqs=$this->WR->vars_save($_POST);
	$this->draw_json_result($reqs);
 }
 private function get_segment_info(){
	$segment=$this->WR->get_segment($_GET['id']);
		 $reqs='ID:'.$segment['id']."<br>";
			$reqs.='Updated :'.$segment['updated']."<br>";
			
			if(isset($segment['vars']))
			{
			 foreach($segment['vars'] as $method=>$variables)
			 {
				$reqs.=''.$method."";
				$reqs.='<ul style="list-style:none;padding:0;margin:0">';
				$i=0;
				foreach($variables as  $v)
				{
				 if($v['use_type'])
					$var_name=$v['code_contains'].' '.$v['code_size'];
				 else
					$var_name=$v['value'];
				 
				  $row=$v['name'].'='.$var_name;
				 $reqs.='<li class="approved'.$v['approved'].'">'.substr($row,0,30).'</li>';
				 $i++;
				 if($i>10)break;
				}
				$reqs.='</ul>';
			 }
			 if($i<count($variables))
			 {
				$reqs.='<div style="color: red">More '.(count($variables)-$i).' variables...</div>';
			 }
			}
			
			$this->draw_html_result($reqs);
 }
 
 private function get_truncate(){
	if($this->WR->isEditor())
		 $reqs=$this->WR->truncate();
		 $this->draw_json_result($reqs);
 }
 
 private function get_delete_segments(){
	if($this->WR->isEditor())
		 $reqs=$this->WR->delete_codes(explode(',',$_GET['ids']));
		 $this->draw_json_result($reqs);
 }
 
 private function get_delete_vars(){
	if($this->WR->isEditor())
		 $reqs=$this->WR->delete_vars(explode(',',$_GET['ids']));
		 $this->draw_json_result($reqs);
 }
 
 private function get_show_segment(){
	$reqs=$this->WR->get_segment($_GET['id']);
	$this->draw_json_result($reqs);
 }
 
 private function get_show_segments(){
	if(substr($_GET['ids'],strlen($_GET['ids'])-1)==',')$_GET['ids']=substr($_GET['ids'],0,(strlen($_GET['ids'])-1));    
	$reqs=$this->WR->get_segments(explode(',',$_GET['ids']));
	$this->draw_json_result($reqs);
 }
 private function get_show_vars(){
	$reqs=$this->WR->get_vars(explode(',',$_GET['ids']));
	$this->draw_json_result($reqs);
 }
 private function get_show_var(){
		$reqs=$this->WR->get_var($_GET['id']);
		$this->draw_json_result($reqs);
 }
 private function get_dashboard_info(){
	$reqs=$this->WR->get_dashboard_info($_GET['from_date'],$_GET['to_date']);
	$this->draw_json_result($reqs);
 }
 private function get_generate_key(){
	$key1=$this->WR->generateSecurityKey();
	$key2=$this->WR->generateSecurityKey();
	 $reqs=array('key'=>$key1,'key2'=>$key2);
	 #$reqs=array('key'=>$this->WR->generateSecurityKey());
	 $this->draw_json_result($reqs);
 }
 private function get_save_segment_position(){
	$reqs=$this->WR->save_segment_position($_POST);
	 $this->draw_json_result(array('result'=>true));
	
 }
}
$WA=new WafAjax($_GET['act']);


?>