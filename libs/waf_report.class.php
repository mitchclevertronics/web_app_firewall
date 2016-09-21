<?php
/* 
 * Class for W.A.F. Display Reports and control segments map UI
 * This product includes PHP software, freely available from <http://www.php.net/software/>
 * Author: Roman Shneer romanshneer@gmail.com
 * 21.09.2016
 */

Class WafReport{
	
	var $log_per_page=20; //bad request count per page
	var $config_file="libs/config.inc.php";
	
	function __construct(){
		//require_once "libs/config.inc.php";
	 
		$config=DB::get_config();
		$this->config=$config;
	
		$this->db=new DB($config['db_host'],$config['db_name'],$config['db_user'],$config['db_pass']);
               
    $this->reload_settings();
		if(!isset($_SESSION['waf_user']))
		{
			header('Location:'.$config['web_root'].'login.php');
			exit();
		}
						
	}   
	public function reload_settings(){
	 $this->waf_learn_status=$this->get_setting('waf_learn_status','0');
	 $this->waf_guard_status=$this->get_setting('waf_guard_status','0');
	 $this->url404=$this->get_setting('url404',$this->config['web_root'].'404.html');
	 $this->waf_security_key=$this->get_setting('waf_security_key',$this->generateSecurityKey());
	 $this->waf_security_key2=$this->get_setting('waf_security_key2',$this->generateSecurityKey());
	 $this->waf_bf=$this->get_setting('waf_bf','0.3');
	 $this->waf_bf_attempt=$this->get_setting('waf_bf_attempt','3');
	 
	}      
	public function generateSecurityKey(){
	 $key=md5(base64_encode(print_r($_SERVER,1).rand(1000,time())));
	 return $key;
	}
	/*
	 * Get Setting, if doesnt exists save default value and return default
	 * @param string $key - key of settings
	 * @paran string $default - recommended default value   
	 * @return string $value - value from setting or default
	 */
	private function get_setting($key,$default)
	{
			$sql="SELECT value FROM waf_settings WHERE name='".$this->db->Q($key,1)."'";
			$result=$this->db->ROW_Q($sql);
			if($result==false)
			{
				 //save default value 
					$this->db->QUERY("INSERT INTO waf_settings (name,value) VALUES ('".$this->db->Q($key,1)."','".$this->db->Q($default,1)."')");
					return $default;
			}else{
					return $result['value'];
			}
	}
	
	/*
	 * get reqursive segments tree
	 * @param int $parent - parent ID - first 0
	 * @return array $segments
	 */
	public function get_segments_tree($parent){

			$sql="SELECT s.*,count(v1.id) as vars_approved1,count(v2.id) as vars_approved0 "
				. "FROM waf_segments s "
							." LEFT JOIN waf_vars v1 ON v1.sid=s.id AND v1.approved=1"
							." LEFT JOIN waf_vars v2 ON v2.sid=s.id AND v2.approved=0 "
							. "WHERE s.parent=".$this->db->Q($parent)." GROUP BY s.id ORDER BY s.id";
			$segments=$this->db->LIST_Q($sql);
		
			if($segments)    
			for($i=0;$i<count($segments);$i++)
			{
				 # echo $segments[$i]['id']."<hr>";
					#$childs_count++;
					$segments[$i]['childs']=$this->get_segments_tree($segments[$i]['id']);
					

			}
			return $segments;
			#return $segments;
	}

	/*
	 * reqursive draw segments tree
	 * @param array $segments
	 * @return string $html
	 */
	public function draw_segments_tree($segments,$lvl)
	{
	
	 #$reqs=$segments['childs'];
				
			 $html='<ul>';
		
			 $row=0;
			foreach($segments as $r)
			{
			
					$html.='<li id="'.$r['id'].'"  lvl='.$lvl.'>';
					$vars_count=($r['vars_approved0']+$r['vars_approved1']);
					if($r['use_type'])$item_name=$r['code_before']." (".$r['code_contains'].") [".$r['code_size']."] ".$r['code_after'];
					else{
					 $item_name=((!empty($r['value']))?$r['value']:'##root##');
					}
					if(($vars_count)||($r['bf']))
					{
					 $addl=array();
					 if($vars_count)$addl[]=$vars_count;
					 if($r['bf'])$addl[]='BF';
					 
					 $item_name.="{".implode(", ",$addl)."}";
					}
					$style="";
					if(!empty($r['segment_x']))$style.='left:'.$r['segment_x'].";right: auto;";
					if(!empty($r['segment_y']))$style.='top:'.$r['segment_y'].";bottom: auto;";
					
					$css_classes="approved".$r['approved'].' use_type'.$r['use_type'].' '.'bf'.(($r['bf']>0)?1:0)
						.(($r['vars_approved0'])?' vars_approved0 ':'')
						.(($r['vars_approved1'])?' vars_approved1 ':'')
						.(($vars_count)?' have_vars':' no_vars')
						.' segment segment'.$r['id'];
					
					$html.='<span class="'.$css_classes.'" segment_id="'.$r['id'].'" segment_parent="'.$r['parent'].'" title="Segment ID:'.$r['id'].'">'.$item_name.'</span>';
				
					if($r['childs'])$html.=$this->draw_segments_tree($r['childs'],($lvl+1));
					$html.='</li>';
					$row++;
			}
			$html.='</ul>';
			return $html;
	}
  
	private function get_vars4segment($id)
	{
			$sql="SELECT * FROM waf_vars WHERE sid=".$this->db->Q($id)." ORDER BY method,approved, name";
		  $vars=$this->db->LIST_Q($sql);
			$result=Array();
			if($vars)
			 foreach($vars as $v)
				$result[$v['method']][]=$v;
			return $result;	
	}
	/*
	 * Get Segment for given array segment ids
	 * @param array $ids - segment_id
	 * @return array $segments
	 */
	public function get_segments($ids)
	{
			$segments=Array();
			foreach($ids as $id)
			{
					$segments[]=$this->get_segment($id);
			}
			return $segments;
	}
	public function get_segments_all(){
	 	$sql="SELECT s.*"
			. "FROM waf_segments s "
			. "order by s.id, s.parent";
		
		$segments=$this->db->LIST_Q($sql);
		return $segments;
	}
  /*
	 * Get One Segment per $id
	 * @param int $id segment_id
	 * return array $segment
	 */      
	public function get_segment($id)
	{
		$sql="SELECT * FROM waf_segments WHERE id=".$this->db->Q($id);								
		$result=$this->db->ROW_Q($sql);
		if($result==false)return false;
		$result['vars']=$this->get_vars4segment($id);
		if(empty($result['code_contains']))
		{
		 $contains='';
		 
		  $containsLetter  = preg_match('/[a-zA-Z]/',    $result['value']);	
			if($containsLetter)
			 $contains.='l';
			 $containsDigit   = preg_match('/\d/',         $result['value']);
			 if($containsDigit)
				$contains.='d';
			 $leaved=  implode(array_unique(str_split(preg_replace('/[0-9]/', '', preg_replace('/[a-zA-Z]/', '', $result['value'])))));
			 
			 $contains.=$leaved;
			 
		 $result['code_contains']=$contains;
		}
		if($result['code_size']==0)
		{
		 $result['code_size']=strlen((string)$result['value']);
		}
		
		return $result;
	}
	
	/*
	* Get Variable per id
	* @param int $id
	* @return array $var
	*/
 public function get_var($id)
 {
    $sql1="SELECT * FROM waf_vars WHERE id=".$this->db->Q($id);
	  $var=$this->db->ROW_Q($sql1);
		if($var)
		{
		 if(empty($var['code_contains']))
		{
		 $contains='';
		 
		  $containsLetter  = preg_match('/[a-zA-Z]/',    $var['value']);	
			if($containsLetter)
			 $contains.='l';
			 $containsDigit   = preg_match('/\d/',         $var['value']);
			 if($containsDigit)
				$contains.='d';
			 $leaved=  implode(array_unique(str_split(preg_replace('/[0-9]/', '', preg_replace('/[a-zA-Z]/', '', $var['value'])))));
			 
			 $contains.=$leaved;
			 
		 $var['code_contains']=$contains;
		}
		
         
			return $var;
			}else{
			return null;
			}     
	}
	
	/*
	 * Get Variables per ids
	 * @param array $ids
	 * @return array $vars
	 */
	public function get_vars($ids)
	{
			$vars=Array();
			foreach($ids as $id)
			{
				 if(!empty($id))
				 {
					$var=$this->get_var($id);
					if($var)$vars[]=$var;
				 }
			}
			
			return $vars;
	}
        
  /*
	 * Get Segments for $parent
	 * @param int $id ParentID
	 * @return array $segments
	 */     
	private function get_segment4parent($id)
	{
		$sql1="SELECT * FROM waf_segments WHERE parent=".$this->db->Q($id);
		$segs=$this->db->LIST_Q($sql1);
		return $segs;
	}
	/*
	 * Find Segments from same branch,
	 * merge they requests and vars to robber segment
	 * @param $id - robber segment_id
	 * @param $code - saved code
	 * @return void
	 */
	private function merge_segment_brothers($id,$code)
	{
		## merge brother segments
		$n_count=count($this->get_segment4parent($id));
		
		$sql1="SELECT id,parent FROM waf_segments WHERE parent IN (SELECT parent FROM waf_segments WHERE id=".$this->db->Q($id).") AND id!=".$this->db->Q($id);
		$segs=$this->db->LIST_Q($sql1);
	
		if($segs)
		foreach($segs as $s)
		{
			if($s['parent']>0)
			{
			$count=count($this->get_segment4parent($s['id']));
		
			if($count==$n_count)
			{		
			
			$sql3="UPDATE waf_vars SET sid=".$this->db->Q($id)." WHERE sid=".$s['id'];
			$this->db->QUERY($sql3);
			
			$sql4="DELETE FROM waf_segments WHERE id=".$this->db->Q($s['id']);
			$this->db->QUERY($sql4);
			}
			}
		}
	}
	/*Remove all data*/
	public function truncate()
	{
			
			$this->db->QUERY("TRUNCATE waf_vars");
			$this->db->QUERY("TRUNCATE waf_segments");
	}
  
	/* Update Variables by one value\code*/
  public function vars_save($data)
	{
		
		$ids=explode(",",$data['ids']);
		
		$sql="UPDATE waf_vars SET ";
                if($data['use'])$sql.="code_contains='".$this->db->Q($data['code_contains'],1)."', code_size=".$this->db->Q($data['code_size']).", ";
                $sql.="approved=".$this->db->Q($data['approved']).", "
                        . "use_type=".$this->db->Q($data['use'])." "
                        . "WHERE id IN (".implode(',',$ids).") ";
					
		$this->db->QUERY($sql);
		
		return array('result'=>true);
	}

	private function save_segment($id,$data)
	{
		
		
		$sql="UPDATE waf_segments SET ";
                if($data['use']==1)
								 $sql.="code_contains='".$this->db->Q($data['code_contains'],1)."',"
									. "code_size=".$this->db->Q($data['code_size']).","
									. "code_before='".$this->db->Q($data['code_before'],1)."',"
									. "code_after='".$this->db->Q($data['code_after'],1)."',";
								
                $sql.="approved=".$this->db->Q($data['approved']).", "
									. "bf=".$this->db->Q($data['bf']).", "
									. "use_type=".$this->db->Q($data['use'])." "
									. "WHERE id=".$this->db->Q($id);
         
		$this->db->QUERY($sql);
	
		
		return array('result'=>true);
	}
	
	/*
	 * Save segment position after dragging
	 * @param array $data['x','y',id]: x,y=left,top (200px), id - segment_id
	 * @return void
	 */
	public function save_segment_position($data){
	 $sql="UPDATE waf_segments SET segment_x='".$this->db->Q($data['x'])."',segment_y='".$this->db->Q($data['y'])."' "
								 . " WHERE id=".$this->db->Q($data['id']);
		$this->db->QUERY($sql);						 
	}
	
	/* Multy Save Segments for one type and options
	 * @param array $ids - SegmentId
	 * @param string $code - AutoType Code
	 * @param bool\int $approved status
	 * @param bool\int $use = use autotype of static value
	 * @return void
	 */
  
	public function save_segments($data)
	{
    $ids=explode(",",$data['ids']);        
		if($data['use']==1)
		{
		 
		//update first
		 $first_id=$ids[0];
		 foreach($ids as $id)
		 {
			$this->save_segment($id,$data);   
		 }
		 $this->robbing_segments($first_id,$ids);
		 $this->robbing_vars($first_id);
		}else{
			
		foreach($ids as $id)
			$this->save_segment($id,$data);
		
		}
		
		return array('result'=>true);
	}
	
	private function robbing_vars($segment_id){
	 $vvars=$this->get_vars4segment($segment_id);
	 if($vvars)
	 {
	 foreach($vvars as $method=>$vars)
	 {
		$nvars=Array();
		foreach($vars as $v)
		{
		 $nvars[$v['name']]=$v['id'];
		}
		sort($nvars);
		if(count($nvars))
		{
		$sql="DELETE FROM waf_vars WHERE sid=".$this->db->Q($segment_id)." AND id NOT IN (".implode(",",$nvars).") AND approved!=1";
		
		$this->db->QUERY($sql);
		}
	 }
	 }
	 
	}
	
	/* 
	 * Robbing Segments
	 * @param int $fid - segment_id master
	 * @param array $ids - segment_id slaves
	 * return void
	 */
	private function robbing_segments($fid,$ids)
	{
	 $fsegment=$this->get_segment($fid);
	 $sql="SELECT id FROM waf_segments WHERE parent=".$fsegment['parent']." AND id!=".$this->db->Q($fid)
		 ." AND use_type=1 and code_contains='".$this->db->Q($fsegment['code_contains'],1)."' AND code_size=".$this->db->Q($fsegment['code_size'])
		 ." AND code_before='".$this->db->Q($fsegment['code_before'],1)."' AND code_after='".$this->db->Q($fsegment['code_after'],1)."'";
	 #echo $sql;
	 $segments=$this->db->LIST_Q($sql);
	 if($segments)
	 foreach($segments as $s)
	 {
		//rob vars
		 $sql1="UPDATE waf_vars SET sid=".$this->db->Q($fid)." WHERE sid=".$s['id'];
		 $this->db->QUERY($sql1);
		//rob child segments
		 $sql2="UPDATE waf_segments SET parent=".$this->db->Q($fid)." WHERE parent=".$s['id'];
		 $this->db->QUERY($sql2);
		 //delete old segment
		 $sql3="DELETE FROM waf_segments WHERE id=".$this->db->Q($s['id']);
		 $this->db->QUERY($sql3);
		 
	 }
	 
	}
	
	
	
	/*
	 * Remove all data of segment 
	 * @param int $id - SegmentId
	 */
	public function delete_code($id)
	{
		$sql="DELETE FROM waf_segments WHERE id=".$this->db->Q($id);
		$this->db->QUERY($sql);

	
		$sql2="DELETE FROM waf_vars WHERE sid=".$this->db->Q($id);
		$this->db->QUERY($sql2);

		return array('result'=>true);
	}
	/*
	 * Remove Segments
	 * @param array $ids SegmentId
	 * @return void
	 */
	public function delete_codes($ids)
	{

			foreach($ids as $id)$this->delete_code($id);
	}
        
	/* Delete Vars per ids
	 * @param array $ids
	 * @return void
	 */
	public function delete_vars($ids)
	{
			//get vars
		 $vars=$this->get_vars($ids);
		 //collect rids
		 $rids=array();
		 if(count($vars))
		 {    
		 foreach($vars as $v)
				 $rids[]=$v->rid;
		 $rids=array_unique($rids);
		//delete vars
		 foreach($ids as $id)$this->delete_var($id);
		
			return array('result'=>true);
		 }else{
			return array('result'=>false);
		 }

	}

	/* Delete Var per id
	 * @param int $id
	 * @return void
	 */
	private function delete_var($id){
			$sql2="DELETE FROM waf_vars WHERE id=".$this->db->Q($id);
$this->db->QUERY($sql2);
	}
	
	/*
	 * Save Setting Value
	 * @param string $key
	 * @param string $status
	 * return void
	 */
	public function save_settings($key,$status){
			$sql="UPDATE waf_settings SET value='".$this->db->Q($status,1)."' WHERE name='".$this->db->Q($key,1)."'";
	$this->db->QUERY($sql);

	}
	
	/*
	 * Get Bad Requests Log
	 * @param array $_GET
	 * return array $logs
	 */
	public function get_logs($get)
	{
		$this->logs_count=$this->get_logs_count($get);
		$this->total_pages=ceil($this->logs_count/$this->log_per_page);
		#echo $this->total_pages."<hr>";

		$sql="SELECT * FROM waf_logs  WHERE 1=1";
		if(!empty($get['from_date']))
		{
			$sql.=" AND created>='".date('Y-m-d 00:00:00',strtotime($get['from_date']))."'";
		}
		if(!empty($get['to_date']))
		{
			$sql.=" AND created<='".date('Y-m-d 23:59:59',strtotime($get['to_date']))."'";
		}
		if(!empty($get['ip']))
		{
			$sql.=" AND ip='".$this->db->Q($get['ip'],1)."'";
		}
		if(!empty($get['url']))
		{
			$sql.=" AND url LIKE '".$this->db->Q($get['url'],1)."%'";
		}
		if(!empty($get['sid']))
		{
			$sql.=" AND sid=".$this->db->Q($get['sid']);
		}
		if(!isset($get['page']))
			$get['page']=1;


		$sql.=" ORDER BY created DESC";
		$sql.=" LIMIT ".$this->log_per_page." OFFSET ".($this->log_per_page*($get['page']-1));
		$logs=$this->db->LIST_Q($sql);
		return $logs;
	}
	public function get_blacklist($get){
		$this->logs_count=$this->get_blacklist_count($get);
		$this->total_pages=ceil($this->logs_count/$this->log_per_page);
		#echo $this->total_pages."<hr>";

		$sql="SELECT * FROM waf_blacklist  WHERE 1=1";
		if(!empty($get['from_date']))
		{
			$sql.=" AND created>='".date('Y-m-d 00:00:00',strtotime($get['from_date']))."'";
		}
		if(!empty($get['to_date']))
		{
			$sql.=" AND created<='".date('Y-m-d 23:59:59',strtotime($get['to_date']))."'";
		}
		if(!empty($get['ip']))
		{
			$sql.=" AND ip='".$this->db->Q($get['ip'],1)."'";
		}
		
		if(!isset($get['page']))
			$get['page']=1;


		$sql.=" ORDER BY created DESC";
		$sql.=" LIMIT ".$this->log_per_page." OFFSET ".($this->log_per_page*($get['page']-1));
		$logs=$this->db->LIST_Q($sql);
		return $logs;
	}
	public function truncate_logs(){
	 	$sql="DELETE FROM waf_logs  WHERE 1=1";
		$this->db->QUERY($sql);
	}
	
	/*
	 * Get Bad Requests Log Count
	 * @param array $_GET
	 * return int $num
	 */
	public function get_logs_count($get)
	{
		$sql="SELECT count(*) as num FROM waf_logs  WHERE 1=1";
		if(!empty($get['from_date']))
		{
			$sql.=" AND created>='".date('Y-m-d 00:00:00',strtotime($get['from_date']))."'";
		}
		if(!empty($get['to_date']))
		{
			$sql.=" AND created<='".date('Y-m-d 23:59:59',strtotime($get['to_date']))."'";
		}
		if(!empty($get['ip']))
		{
			$sql.=" AND ip='".$this->db->Q($get['ip'],1)."'";
		}
		if(!empty($get['url']))
		{
			$sql.=" AND url LIKE '".$this->db->Q($get['url'],1)."%'";
		}
		if(!empty($get['sid']))
		{
			$sql.=" AND sid=".$this->db->Q($get['sid']);
		}
		$c=$this->db->ROW_Q($sql);

		return $c['num'];
	}
	public function get_blacklist_count($get)
	{
		$sql="SELECT count(*) as num FROM waf_blacklist WHERE 1=1";
		if(!empty($get['from_date']))
		{
			$sql.=" AND created>='".date('Y-m-d 00:00:00',strtotime($get['from_date']))."'";
		}
		if(!empty($get['to_date']))
		{
			$sql.=" AND created<='".date('Y-m-d 23:59:59',strtotime($get['to_date']))."'";
		}
		if(!empty($get['ip']))
		{
			$sql.=" AND ip='".$this->db->Q($get['ip'],1)."'";
		}
		if(!empty($get['sid']))
		{
			$sql.=" AND sid=".$this->db->Q($get['sid']);
		}
		
		$c=$this->db->ROW_Q($sql);

		return $c['num'];
	}
	public function delete_blacklist($id)
	{
		$this->db->QUERY("DELETE FROM waf_blacklist WHERE id=".$this->db->Q($id));
	}
	/*
	 *Experimental transfer request to words array for using GoogleGraphs WordTree	
	 */
	public function transform_tree2words($reqs,$parent_code='')
	{
		$words=Array();
		foreach($reqs as $r)
		{
			$words[]=$parent_code.$r['code'];
			if($r['childs'])
			{
				$childs=$this->transform_tree2words($r['childs'],$parent_code.$r['code'].' ');
				$words=array_merge($words,$childs);
			}	
		}
		return $words;
	}
	/*
	 * Show Accepted\New segments Statistics for Dashboard
	 * @return array 
	 */
	public function get_segments_statistics(){
		$sql="SELECT approved,count(id) as num FROM waf_segments "
		
			. "GROUP BY approved";

		$segments=$this->db->LIST_Q($sql);
		$statuses=array(0=>0,1=>0);
		if($segments)
		foreach($segments as $s)
		{
			$statuses[$s['approved']]=$s['num'];
		}
		return ($statuses);
	}
	/*
	 * Show Accepted\New vars  Statistics for Dashboard
	 * @return array 
	 */
	public function get_vars_statistics(){
		$sql="SELECT approved,count(id) as num FROM waf_vars "
			
			." GROUP BY approved";

		$segments=$this->db->LIST_Q($sql);
		$statuses=array(0=>0,1=>0);
		if($segments)
		foreach($segments as $s)
		{
			$statuses[$s['approved']]=$s['num'];
		}

		return ($statuses);
	}
	/*
	 * Show Bad Request Stoped daily statistics
	 * @return array 
	 */
	public function get_logs_statistics($from_date,$to_date){
		$sql="SELECT DATE_FORMAT(created,'%Y-%m-%d') as day,count(id) as num
					FROM `waf_logs`
					WHERE DATE_FORMAT(created,'%Y-%m-%d')>'".date('Y-m-d',strtotime($from_date))."' AND DATE_FORMAT(created,'%Y-%m-%d')<'".date('Y-m-d',strtotime($to_date))."'
					GROUP BY day
					ORDER BY day";
		$logs=$this->db->LIST_Q($sql);
		$days=Array();
		foreach($logs as $l)
		{
			$days[$l['day']]=$l['num'];
		}
		$result=Array();
		for($t=strtotime($from_date);$t<strtotime($to_date);$t+=86400)
		{
			if(isset($days[date('Y-m-d',$t)]))$result[date('Y-m-d',$t)]=$days[date('Y-m-d',$t)];
			else $result[date('Y-m-d',$t)]=0;

		}
		return $result;
	}
	public function get_log_url_statistics($from_date,$to_date){
		$sql="SELECT url, count( id ) AS num
		FROM `waf_logs`
		WHERE DATE_FORMAT( created, '%Y-%m-%d' ) > '".date('Y-m-d',strtotime($from_date))."' AND DATE_FORMAT( created, '%Y-%m-%d' ) < '".date('Y-m-d',strtotime($to_date))."'
		GROUP BY url
		ORDER BY num DESC
		LIMIT 15";
		$logs=$this->db->LIST_Q($sql);
		return $logs;
	}
	public function isEditor(){
	 if($_SESSION['waf_user']['editor']==1)return true;
	 else return false;
	}
	public function get_dashboard_info($from_date,$to_date){
	 $data=Array();
	 $data['logs']=$this->get_logs_statistics($from_date,$to_date);
	 $data['logs_url']=$this->get_log_url_statistics($from_date,$to_date);
	 return $data;
	}
	
}
/*Help function for debug with stop*/
function pre($var)
{
    print "<pre>";
    print_r($var);
    print "</pre>";
    die("<hr>");
}
/*Help function for debug no stop*/
function pr($var)
{
    print "<pre>";
    print_r($var);
    print "</pre>";
    echo "<hr>";
}

?>