<?php
/*
 * script for organization reverce-proxy and control (frontend part)
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */

require_once 'libs/db.inc.php';
require_once 'libs/waf_helper.class.php';
session_start();

if(isset($_SERVER['HTTP_WAF_KEY2']))die("Guru in loop. Deep meditation...<hr>");	

/* WAF Layer logic */
Class WAF extends WAFHelper{
    private $step=0;
    private $error_url;
		public $web_root;
    //activate db connection
    function __construct(){
			$config=DB::get_config(false);
			if($config)
			{
				$this->web_root=$config['web_root']; 
				$this->db=new DB($config['db_host'], $config['db_name'], $config['db_user'], $config['db_pass']);
				//need make something if no db
				$this->waf_learn_status=$this->get_setting('waf_learn_status',0);
				$this->waf_guard_status=$this->get_setting('waf_guard_status',0);
				$this->waf_security_key=$this->get_setting('waf_security_key',0);
				$this->waf_security_key2=$this->get_setting('waf_security_key2',0);
				$this->waf_bf=$this->get_setting('waf_bf','0.3');
				$this->waf_bf_attempt=$this->get_setting('waf_bf_attempt','3');
				$this->waf_bf_bantime=$this->get_setting('waf_bf_bantime','30');
				$this->error_url=$this->get_setting('url404','');
			}else{
			 $this->web_root==false;
			}
    }
    /*
     * Request from Layer to Real Site
     * @param string $url - request to url
     * @param array  $vars- variables
     * @param string $method
     */
    public function curl_request($request_data,$method){
		 
           $url=$request_data['url'];
		   
		   $u=parse_url($url);
		   if(!isset($u['host']))$u['host']='http://localhost/';
		   if($u['host']!=$_SERVER['SERVER_NAME'])
			   $url=$_SERVER['SERVER_NAME'].$url;
		 
			$vars=$request_data['vars'];
			if(($request_data['method']=='GET')&&count($vars))
				$url.="?".http_build_query($vars);
			
			
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			
			$cookie_file= self::get_cookie_file(session_id());
			#pr($cookie_file."::".filemtime($cookie_file));
			
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
			curl_setopt($ch, CURLOPT_VERBOSE, true);	
			#curl_setopt($ch, CURLINFO_HEADER_OUT, true);	
			
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
						
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'WAF_KEY2:'.$this->waf_security_key2, //set second key
                'User-Agent: '.$_SERVER['HTTP_USER_AGENT'],
              ));
			if($request_data['method']=='POST')
            {
						
			### ADD files to post
			  if(isset($_FILES)&&(count($_FILES)))
				{
				 foreach($_FILES as $fname=>$fval)
				 {
					$imginfo   = getimagesize($fval['tmp_name']);
					$vars[$fname]=curl_file_create($fval['tmp_name'],$imginfo['mime'],$fval['name']);
				 }
				}

				curl_setopt($ch,CURLOPT_POST, count($vars));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $vars);
            }
          
			$contents= curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
           if($httpcode=='302')
            {
			$redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);			 
            header("Location:".$redirect_url);
            exit();
            }    
			$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			
			$error=curl_error($ch);
			curl_close($ch);
			$this->header_content_type($content_type);
            return $contents;
    }
	
	private function header_content_type($content_type){
		header("Content-Type: ".$content_type);
	}

    #### CHECK First Security Key for auth request from htaccess ####
	private function check_security_key($http_request){
	 if((!isset($_SERVER['HTTP_WAF_KEY']))||($this->waf_security_key!=$_SERVER['HTTP_WAF_KEY']))
	 {
		 $text="Request to Layer withour KEY";
		 $http_request=$this->log_bad_request($http_request,"sec_key",$text,0);
		 return $http_request;
	 }
	 if($bl=$this->check_ip_blacklist($_SERVER['REMOTE_ADDR']))
	 {
		 
		 $text="IP ".$_SERVER['REMOTE_ADDR']." blacklisted from ".$bl['created'];
		 
		 $http_request=$this->log_bad_request($http_request,"blacklist",$text,0);
		 
		 return $http_request;
	 }
	 return false;
	}
	
	private function check_ip_blacklist($ip){
		$sql="SELECT * FROM waf_blacklist WHERE ip='".$this->db->Q($ip,1)."'";
		if($this->waf_bf_bantime!=0)$sql.=" AND created>'".date('Y-m-d H:i:s',strtotime("-".$this->waf_bf_bantime." days"))."'";
		$bl=$this->db->ROW_Q($sql);
		return $bl;
	}

    /* Prepare paths and variables from request via AnalizeURL */
    public function prepare_request(){
           $u=explode('?',$_SERVER['REQUEST_URI']);
					 
           $url=$u[0];
					 if($_SERVER['REQUEST_METHOD']=='POST'){
						if(isset($u[1]))
						{
						 $url.="?".$u[1];
						}
					 }
					 
            $http_request=array('url'=>$url,
                                'vars'=>($_SERVER['REQUEST_METHOD']=='POST')?$_POST:$_GET,
                                'method'=>$_SERVER['REQUEST_METHOD']);
				
            if(!$this->web_root)return $http_request;
						
						$hr=$this->check_security_key($http_request);
						
						if($hr)return $hr;
						
            $data=$this->analize_request($http_request);
            $data['url']='http://'.$_SERVER['SERVER_NAME'].$data['url'];
            
            return $data;
    }
    
    /*
     * Clear Segments Tree from nonapproved
     * @param array $tree
     * @return array $tree
     */
    private function leave_legal_only($tree){
        if(count($tree)==0)return $tree;
        $max=max(array_keys($tree));
			
        for($m=0;$m<=$max;$m++)
        {
            if(isset($tree[$m]))
				if($tree[$m]['approved']==0)unset($tree[$m]);
          
        }
        return $tree;
    }
    
    /* Process Requst Learning and Guarding  
     * @param array $http_request = url, vars, method
     * @return array $http_request = url, vars, method = Modified
     */
    private function analize_request($http_request){
					
            //Translate Vars to simple array format
            $http_request['vars']=$this->prepare_vars(array(),$http_request['vars'],array());
            $uaa=$this->url2array($http_request['url']);
            
            //load known segments
						
            $tree=$this->load_segments_from_db($uaa);
						
						
            //learn new segments
            $tree=$this->learn2segments($uaa,$tree);
			
			$last_segment_id=$tree[count($tree)-1]['id'];
         
            //learn new vars
            if(count($http_request['vars']))$this->learn2vars($http_request);
						
            //compare with known segments and changed request if bad
			$http_request=$this->guard2segments($http_request,$uaa,$tree);
			//BF tests
			$http_request=$this->guard2bf($http_request,$tree);
           
            //compare with known vars and changed request if bad		
			if(count($http_request['vars']))$http_request=$this->guard2vars($http_request,$last_segment_id);
           
						
            return $http_request;

    }
    /*
     * Compare Real and Known segments tree
     * Change URL if bad
     * @param array $uaa - real 
     * @param array $tree - known
     * @return void
     */
    private function learn2segments($uaa,$tree){
    
        if($this->waf_learn_status)
            { 
            #$max=max(array_keys($tree))+1;
            $max=count($uaa);
						
            if(count($tree)!=$max)
            {
                
                for($i=0;$i<$max;$i++)
                {
                    if(!isset($tree[$i]))
                    {
												
                        $parent=($i>0)?$tree[$i-1]['id']:0;
                      
												#echo $uaa[$i].":".$parent."<hr>"
                         $inid=$this->insert_segment($uaa[$i],$parent,$i,$uaa[$i]); 
                        
                         $tree[$i]=$this->get_segment($inid);
                    }
                   
                }
            }
            
             $this->segment_id=$tree[$max-1]['id']; 
            }else{
                $this->segment_id=0;
            }
			return $tree;			
    }
    
   
    
   
    /* 
     * Save Request Variables 
     * @param array $nvars - prepared variables
     * @return void
     */
    private function learn2vars($request){
		 $nvars=$request['vars'];
		 
         if($this->waf_learn_status)
            { 
             #$nvars=$this->prepare_vars(array(),$vars,array());
                foreach($nvars as $var_name=>$var_val)
                {
                    $this->save_var2db($request['method'],$this->segment_id,$var_name,$var_val);
                } 
            }
    }
    
		
		
    /* 
     * Compare Segments and Change URL if not equal
     * @param array $nvars - prepared variables
     * @return string $url
     */
    private function guard2segments($http_request,$uaa,$tree){
       
        if($this->waf_guard_status)
            { 
			
            ksort($tree);
							 
            
						$mmm=max(count($tree),count($uaa));
						$uknown=Array();
						for($i=0;$i<$mmm;$i++)
						{
						 if(!isset($tree[$i]))
						 {
							$uknown[]='Unknow segment '.$uaa[$i];
						 }elseif($tree[$i]['approved']){
							if($tree[$i]['use_type']==0)
							{
							 if($tree[$i]['value']!=$uaa[$i])
							 {
								$uknown[]='Segment static '.$tree[$i]['id']." not equal values: ".$tree[$i]['value']."!=".$uaa[$i];
								$segmentsids[]=$tree[$i]['id'];
							 }
							}else{
							 if(!$this->compare($uaa[$i],$tree[$i]))
							 {
								$uknown[]='Segment auto '.$tree[$i]['id']." not equal values: ".$tree[$i]['code_before']."{".$tree[$i]['code_contains']."}[".$tree[$i]['code_size']."]".$tree[$i]['code_after']."!=".$uaa[$i];	
								$segmentsids[]=$tree[$i]['id'];
							 }
							 
							}
							
						 }else{
							$segmentsids[]=$tree[$i]['id'];
							$uknown[]='Segment '.$tree[$i]['value']." not approved";
						 }
						}
						#$max=count($uaa);
					#	die($mmm."<hr>");
            if(count($uknown))
            {
						
			$text="Unknow segment: ".print_r($uknown,1);
			$http_request=$this->log_bad_request($http_request,"segment",$text,$segmentsids[0]);
            }
            }
            return $http_request;
    }
		
   private function guard2bf($http_request,$tree){
       $max_bf_diff=(float)$this->waf_bf;
	   $max_bf_attempt=(int)$this->waf_bf_attempt;
	  
	 if($this->waf_guard_status)
			 { 
			$segment=array_pop($tree);
			$sid=$segment['id'];
			if($segment['bf'])
			{
			//log new cacherecord	
			 $time=microtime(true);
			 $this->db->QUERY("INSERT INTO waf_bf_cache(sid,ip,tt) VALUES (".$sid.",'".$this->db->Q($_SERVER['REMOTE_ADDR'],1)."',".$time.")");

			//calculate logs
			$stime=($time-$max_bf_diff);

			 $sql="SELECT count(id) as num FROM waf_bf_cache WHERE sid=".$sid." AND ip='".$this->db->Q($_SERVER['REMOTE_ADDR'],1)."' AND tt>".$stime;
			 $bc=$this->db->ROW_Q($sql);
			 if($bc['num']>=$max_bf_attempt)
			 {
				$text='Registered '.$bc['num'].' requests from '.$this->db->Q($_SERVER['REMOTE_ADDR'],1).' , in period '.$max_bf_diff." sec";
				#echo $text;
				$this->save2blacklist($sid,$_SERVER['REMOTE_ADDR']);
				$http_request=$this->log_bad_request($http_request,'BF',$text,$sid);
			 }else{
				$this->db->QUERY('DELETE FROM waf_bf_cache WHERE tt<'.$stime); 
				}
			}
				 
			 }
            return $http_request;
    }
	
	private function save2blacklist($sid,$ip){
		$this->db->QUERY("INSERT INTO waf_blacklist(sid,ip,created) VALUES (".$this->db->Q($sid).",'".$this->db->Q($ip,1)."',NOW())");
	}
	
    /*
     * Compare Vars via Request and Change URL,Vars if not equal
     * @param array $http_request $url $method $vars
     * @param array $vars
     * @param string $method - GET\POST
     */
    private function guard2vars($http_request,$segment_id){
        if($this->waf_guard_status)
            { 
            $trust=true;
          
                $stoped_vars=Array();
                foreach($http_request['vars'] as $vname=>$vval)
                {
					$var=$this->load_var(0,$http_request['method'],$vname);//load default first
                    if(!$var)$var=$this->load_var($segment_id,$http_request['method'],$vname); //load regular
									
                    if($var['approved']==1)
                    {
									
                        if(($var['use_type']==0)&&($var['value']!=$vval))
                        {
							$stoped_vars[$vname]=' static value not equal';
                            $trust=false;
                        }elseif($var['use_type']==1){
                           
                            if(!$this->compare($vval,$var))
                            {
							$stoped_vars[$vname]=' auto value not equal '.$vval."!=".print_r($var,1); 
                            $trust=false;
                            }
                        }
                    }else{
						$stoped_vars[$vname]=' value not approved'; 
                        $trust=false;
                    }
                }
           
            if($trust==false)
            {
			 $text='Segment ID: '.$segment_id."; Vars not accepted: ".print_r($stoped_vars,1);
			 $http_request=$this->log_bad_request($http_request,'var',$text,$segment_id);
            }
            }

            return $http_request;
    }
   
    /*
     * Load Segments and filters into URL Array
     * @param array $uaa
     * @return array $tree 
     */
     private function load_segments_from_db($uaa)
     {
		$max=count($uaa);

		$seqs=$this->search_segments($uaa,$max);

		$tree=Array();

		if($seqs)
			foreach($seqs as $s)
				$tree[$s['lvl']]=$s;
             
		if(count($tree)<$max)
		{

			for($k=0;$k<$max;$k++)
			{
				if(!isset($tree[$k]))
				{
				$parent=($k==0)?0:isset($tree[$k-1])?$tree[$k-1]['id']:0;
				//found autotype via parent
				$filter=$this->find_segment_type($parent,$uaa[$k]);
				if($filter)$tree[$k]=$filter;
				}
			}
		}
            #print_r($tree);
            return $tree;
     }
    
    /* 
     * Searching segments Tree in DB via values only
     * @param array $uaa - segments part array
     * @param int $max 
     * @return array $segs
     */
    private function search_segments($uaa,$max)
    {
         $sql="SELECT * FROM waf_segments WHERE use_type=0 AND (";
          #  $max=count($uaa);
            foreach($uaa as $k=>$u)
            {
                    $sql.="(lvl=".$k." AND value='".$u."')";
                    if($k!=($max-1))$sql.=" OR ";
            }
            $sql.=")";
          
						#echo $sql;
            $segs=$this->db->LIST_Q($sql);
						#pre($segs);
            return $segs;
    }
    
    
    
    /* 
     * Get Segment From DB per id 
     * @param int $segment_id - segment_id
     * @return object $segment
     */
    private function get_segment($segment_id){
        $sql2="SELECT * FROM waf_segments WHERE id=".$this->db->Q($segment_id);
        return $this->db->ROW_Q($sql2);
    }
    
    /* 
     * Insert Segment to DB 
     * @param string $code
     * @param int $parent
     * @param int $lvl
     * @param string $value
     * @param int $lp
     * @return int $segment_id;
     */
    private function insert_segment($code,$parent,$lvl,$value)
    {
        if(empty($code)&&($parent==0))$sql1="INSERT INTO waf_segments(parent,updated,lvl,value) VALUES (".$this->db->Q($parent).",NOW(),".$this->db->Q($lvl).",'".$this->db->Q($value,1)."')";
		else $sql1="INSERT INTO waf_segments(parent,updated,lvl,value) VALUES (".$this->db->Q($parent).",NOW(),".$this->db->Q($lvl).",'".$this->db->Q($value,1)."')";
         $segment_id=$this->db->INSERT($sql1);
         return $segment_id;
        
    }
    
    
    
    /*
     * Find via parent and segment text autotype
     * @param int $parent - segment_id of parent segment
     * @param string $segment - text of segment
     * @return array segment
     */
    private function find_segment_type($parent,$segment)
    {
        //found all parent filters
        $sql="SELECT * FROM waf_segments WHERE approved=1 AND use_type=1 AND parent=".$this->db->Q($parent);
		
        $segments=$this->db->LIST_Q($sql);

        $filters=Array();
        if($segments)
        {
        foreach($segments as $s)
        {
           //comparing filter text with code 
				  
           if($this->compare($segment,$s))$filters[]=$s;
        }

        $filter=array_shift($filters);
        return $filter;
        }else return false;

    }
	
    /*
     * Recursive extract variables, need for implement all array-style variables by strings
     * @param array $nvars - result array, for first call must be empty array = need for reqursive algoritm
     * @param array $vars - variables 
     * @param array $parents parent ids = need for reqursive algoritm
     */
    private function prepare_vars($nvars,$vars,$parents=array())
    {
         $this->step++;

        foreach($vars as $var_name=>$var_val)
            {
                if(is_array($var_val))
                {
                 $parents[]=$var_name;
                 $nvars=$this->prepare_vars($nvars,$var_val,$parents);
                }else{

                if(count($parents)>0)
                {
                    $names=$parents;
                    $names[]=$var_name;

                    for($n=1;$n<count($names);$n++)
                    {
                        $names[$n]='['.$names[$n].']';
                    }
                    $vn=implode('',$names);
                }else{
                    $vn=$var_name;
                }

                $nvars[$vn]=$var_val;

                }	


            }
            ksort($nvars);
            return $nvars;
    }
   
    /*
     * Load Variable Record via RequestID and VarName
     * @param int $rid - RequestID
     * @param string $var_name - VarName
     * @return object waf_vars
     */
    private function load_var($sid,$method,$var_name)
    {
        $v=$this->db->ROW_Q("SELECT * FROM waf_vars WHERE sid=".$this->db->Q($sid)." AND method='".$this->db->Q($method,1)."' AND name='".$this->db->Q($var_name,1)."'");
        return $v;
    }
    
    /*
     * Save into DB varible if new per name and request 
     * @param int $rid = request_id
     * @param int $sid - segment_id
     * @param string $var_name - variable name
     * @param string $var_val - variable value
     * @return void
     */
    private function save_var2db($method,$sid,$var_name,$var_val){
		//load default var
		$v=$this->load_var(0,$method,$var_name);
		if(!$v)$v=$this->load_var($sid,$method,$var_name);//after that load regular var
		
		$var_length=strlen($var_val);		
        if($v)
        {
			if(($v['approved']==0)&&($v['code_size']<$var_length))$update_length=$var_length;
			else $update_length=$v['code_size'];
				
			$sql="UPDATE waf_vars SET updated=NOW(),code_size=".$this->db->Q($update_length)." WHERE id=".$this->db->Q($v['id']);
			$this->db->QUERY($sql);
        }else{
					
		if(is_array($var_val))$var_val=json_encode($var_val);
		$sql="INSERT INTO waf_vars (name,updated,value,method,sid,code_size) VALUES ('".$this->db->Q($var_name,1)."',NOW(),'".$this->db->Q($var_val,1)."','".$this->db->Q($method,1)."',".$this->db->Q($sid).",".$this->db->Q($var_length).")";

		$this->db->INSERT($sql);
        }
		
    }
    /*
     * Help func. for save queries from layer (waf.php) part to file
     * @param string $str
     */
    private function loger($str){
        $f=fopen("waf_log.txt","a");
        fwrite($f, date('H:i:s d-m-Y')." ".$str."\n");
        fclose($f);
    }
    /*
     Compare text via given filter code
     *@param string $segment - text
     *@param array $data - autotype 
     *@return bool $result;
     */
    private function compare($segment,$data)
    {
		if($data['code_contains']=='e')return true; //exception rule
		
        if(isset($data['code_before'])&&(!empty($data['code_before'])))
        {
            if(strtolower(substr($segment,0,strlen($data['code_before'])))!=strtolower($data['code_before']))
                    return false;
            $segment=substr($segment,strlen($data['code_before']));
        }
       

        if(isset($data['code_after'])&&(!empty($data['code_after'])))
        {
			
            if(strtolower(substr($segment,strlen($segment)-strlen($data['code_after']),strlen($segment)))!=strtolower($data['code_after']))
                    return false;
            $segment=substr($segment,0,strlen($segment)-strlen($data['code_after']));
        }
		
		$containsLetter  = preg_match('/[a-zA-Z]/',    $segment);	
		
		if((strpos($data['code_contains'],'l')===false)&&($containsLetter))
		{
				return false;
		}
		
		$containsDigit   = preg_match('/\d/',          $segment);
		
		if(($containsDigit)&&(strpos($data['code_contains'],'d')===false))
		{
				return false;
		}
		
	
		$leaved=  preg_replace('/[0-9]/', '', preg_replace('/[a-zA-Z]/', '', $segment));
		if(!empty($leaved))$leaved=implode("",array_unique(str_split($leaved)));
		
		
		$special_conteins=str_replace('l','',str_replace('d','',$data['code_contains']));
		
		if(strlen($special_conteins)>0)
		{
			$h_persent=false;
			if(strstr($special_conteins,'%'))
			{
				$h_persent=true;
				$special_conteins=str_replace('%','',$special_conteins);
				$leaved=str_replace('%','',$special_conteins);
			}
			
			
			$lv=  preg_replace('/['.$special_conteins.']/', '', $leaved);
			//stell have not allowed chars
			if(strlen($lv)>0)return false;
		}elseif(strlen($leaved)) return false;
		
		
        //check size
        if($data['code_size']>0)
        {
                if(strlen($segment)>$data['code_size'])return false;
        }
        return true;
    }
		
		/*
		 * Save Stoped Request
		 * @param array $http_request - [url,vars,method]
		 * @param string $type - type of allert
		 * @param string $reason - information about attack
		 * @param int $sid - SegmentID
		 * @return array $http_request - [url,vars,method] - changed to 404 page
		 */
		private function log_bad_request($http_request, $type, $reason, $sid=0)
		{
		if(empty($sid))	$sid=0;	
		$data=Array();
        $data['request']=$http_request;
        
        $data['server']=Array('REQUEST_URI'=>$_SERVER['REQUEST_URI'],
                           'REQUEST_METHOD'=>$_SERVER['REQUEST_METHOD'],
                           'REMOTE_ADDR'=>$_SERVER['REMOTE_ADDR'],
                           'HTTP_REFERER'=>isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'',
                           'HTTP_USER_AGENT'=>$_SERVER['HTTP_USER_AGENT']
                           );
        
        
        $content= base64_encode(json_encode($data));
        $reason=base64_encode($reason);      

        $this->db->INSERT("INSERT INTO waf_logs (content,type,created,ip,url,reason,sid) VALUES ('".$this->db->Q($content,1)."','".$this->db->Q($type,1)."',NOW(),'".$this->db->Q($_SERVER['REMOTE_ADDR'],1)."','".$this->db->Q($http_request['url'],1)."','".$this->db->Q($reason,1)."',".$this->db->Q($sid).")");
			$http_request['url']=$this->error_url;
			$http_request['vars']=Array();
			
			return $http_request;
		}
	
}

###### SHIVA DANCE ####
$t=  microtime(true);
$Waf=new WAF;
if($Waf->web_root==false)
{
 
 die("<center><h1>WAF Guru retreat....</h1><p>No config.inc.php file</p></center>");
}elseif(substr($_SERVER['REQUEST_URI'],0,5)!=$Waf->web_root)
{
    ## code start run here
    $request_data=$Waf->prepare_request();
	
    $html=$Waf->curl_request($request_data,$_SERVER['REQUEST_METHOD']);
    echo $html;
}else{
    die("Guru fly in unknown space");
}
#die($httpcode."<hr>");
?>