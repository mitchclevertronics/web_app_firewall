<?php
/*
 * script for proxy layer (frontend library)
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
/* Class with Basic function for WAF Layer*/
abstract Class WAFHelper{
   
	static function get_cookie_file($id)
		{
		 $cookie_file= dirname($_SERVER['SCRIPT_FILENAME']).'/sessions/'.$id.'.txt';
			if(strstr($cookie_file,"C:/"))
			{
			 $cookie_file=str_replace('/','\\',$cookie_file);
			}
			return $cookie_file;
		}	
     /*
         * Get Setting, if doesnt exists save default value and return default
         * @param string $key - key of settings
         * @paran string $default - recommended default value   
         * @return string $value - value from setting or default
         */
        public function get_setting($key,$default)
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
    public function url2array($url)
    {
         $u=explode('?',$url);
          $url=$u[0];
          $ua=explode("/",$url);
           
						if((count($ua)>1)&&(empty($ua[count($ua)-1])))
						 unset($ua[count($ua)-1]);
						return $ua;
					
    }
		/*
		private function loger($str)
		{
		 $f=fopen("waf_log.txt","a");
			fwrite($f,$str."\n\n");
		 fclose($f);
		}*/
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
if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}
?>