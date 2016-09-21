<?php
/* 
 * Class DB MYSQL
 * This product includes PHP software, freely available from <http://www.php.net/software/>
 * Author: Roman Shneer romanshneer@gmail.com
 * 1.02.2012
 * changed 01.11.2015
 */

/*############ database basic interface ####################*/
Class DB
{
        var $conn;
        function DB($db_host,$db_name,$db_user,$db_pass)
        {
        #global $db_host,$db_name,$db_user,$db_pass;
       # die($db_name."<hr>");
        #print $db_host.":".$db_user.":".$db_pass."<hr>";
        #$this->conn = mysql_connect($db_host,$db_user,$db_pass);
           
        if(!$this->conn = @mysqli_connect($db_host,$db_user,$db_pass,$db_name))
        {
        die("<div style='color:red'>Imporstable connect to db server ".$db_host." dbname ".$db_name."</div>");
        exit();
        }
        #die($db_name."<hr>");
        #$rezdb=mysqli_select_db($db_name,$this->conn);
    	#if($rezdb==false)die("<center><h1 style='color:red'>Database not created!</h1><br>Remove conf/config.php for start installation again</center>");
		#die($rezdb."<hr>");
        }
                function redirect($f)
                {
                header("Location:".$f);
                exit();
                }
        #############################
        function QUERY($sql)
        {
         #print $sql."\n";

        $result=mysqli_query($this->conn,$sql);
        #print $result."<hr>";
        if($result)return $result;
        else $this->log_db_error($sql,mysqli_error($this->conn));
        }
        #############################
        function LIST_Q($sql)
        {
        #print $sql."<hr>";
        $result=$this->QUERY($sql);

        if(mysqli_num_rows($result)==0)return false;

        while($row=mysqli_fetch_assoc($result))
                {
                $data[]=$row;
                }

        return $data;
        }
				
        ###########################
        function ROW_Q($sql)
        {
          #  echo $sql;
        $result=$this->QUERY($sql);
        if(@mysqli_num_rows($result)==0)return false;

        return mysqli_fetch_assoc($result);
        }
        function Q($sql,$str=false)
        {
        if(($str==false)&&(!is_integer($sql)))
				 {
					 $value=-1;
				 }
        return mysqli_real_escape_string($this->conn,$sql);
        }
				
				function INSERT($sql){
				 
				 $result=$this->QUERY($sql);
				 
				 $id=(int)mysqli_insert_id($this->conn);
         
				 return $id;
				}
				
        function log_db_error($sql,$message)
        {
        print "<div style='border:solid black 1px;'><div style='border:solid blue 1px;'>".$sql."</div><div style='border:solid red 1px;'>".$message."</div><div style='border:solid green 1px;'>".date("h:i d-m-y")."</div></div><br><br>\n";
        #$f=fopen("../conf/sql_error.html","a");
        #fwrite($f,"<div style='border:solid black 1px;'><div style='border:solid blue 1px;'>".$sql."</div><div style='border:solid red 1px;'>".$message."</div><div style='border:solid green 1px;'>".date("h:i d-m-y")."</div></div><br><br>\n");
        #fclose($f);
        }
        function NUM_ROWS($result)
        {
        	return mysqli_num_rows($result);
        }
				static function get_config($redirect=true)
				{
					$config_file='inc/config.inc.php';
					if(file_exists($config_file))
					{
					 require_once $config_file;
					 return $config;
					}else{
					 if($redirect)
					 {
					 header("Location:install.php");
					 exit();
					 }else{
						return false;
					 }
					}
		
				}
}
?>