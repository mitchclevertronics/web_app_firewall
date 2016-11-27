<?php
/*
 * script for instal WebAppFirewall (backend library)
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
Class WafInstaller{
 function WafInstaller(){
	
	if(file_exists("inc/config.inc.php"))
	{
	 header("Location:installed.php");
	 exit();
	}
 }
 public function try_install_waf($post){
	 
	if(!$this->check_user_email($post))return 'Emails should be not empty';
	if(!$this->check_user_pass($post))return 'Passwords should be equal and not too short';
	 
	 if($this->check_db_connect($post))
	 {
		
		$dbname_res=$this->check_db_name($post);
		
		switch($dbname_res)
		{
		 case 1:
			if($this->create_db_structure($post))
			{
			 if($this->save_user($post))
			 {
				if($this->create_config_file($post))
				{
				 $this->redirect2success();
				}else{
				 return 'Impossible create config file, check directory "libs" write permissions';
				}
			 }else{
				return 'Impossible save first user';
			 }
			}else{
			 return 'Impossible import data';
			}
		 break;
		 case 2:
			return 'Impossible Create Database, check DbUser Permisions, or try create manually';
		 break;
		 case 3:
			return 'Database already exists, Switch off option "Create New Database"';
		 break;
		}
	 }else{
		return 'Impossible connect to database, check db parameters';
	 }
	}
	private function check_db_connect($post){
	 $this->connection=@mysql_connect($post['dbhost'], $post['dbuser'],$post['dbpass']);
	 if(!$this->connection){
		return false;
	 }else return true;
	}
	
	private function check_db_name($post)
	{
	 
	 
	 $result=mysql_select_db($post['dbname'],$this->connection);
	 if(isset($post['new_db']))
	 {
		if($result)return 3;
		else{
		 mysql_query("CREATE DATABASE ".$post['dbname'],$this->connection);
		 $result=mysql_select_db($post['dbname'],$this->connection);
		 if(!$result)
		 {
			return 2;
		 }else{
			return 1;
		 }
		}  
	 }else{
		if(!$result)
		 {
			return 2;
		 }else{
			return 1;
		 }
	 }

	 
	}
	private function check_user_email($post){
	 if(($post['keep_db']=='keep')&&empty($post['email']))return true;
	 
	 if(empty($post['email'])==true)return false;
	 if(!strstr($post['email'],'@'))return false;
	 return true;
	}
	private function check_user_pass($post){
	 if(($post['keep_db']=='keep')&&empty($post['email']))return true;
	 if(empty($post['password'])==true)return false;
	 if($post['password']!=$post['password1'])return false;
	 return true;
	}
	private function create_db_structure($post){
	 
	 if(($post['keep_db']=='keep')&&(!isset($post['new_db'])))return true;
	 
	 $content=file_get_contents("inc/waf.sql");
		$sql_comm=explode(";",$content); 
		foreach($sql_comm as $comm)
		{
		mysql_query($comm,$this->connection);
		}
		return true;
	 
			#header("Location:".$config['web_root']);
	}
	private function save_user($post)
	{
	 if((!empty($post['email']))&&(!empty($post['password'])))
	 {
		 $user=$this->getUserByEmail($post['email']);
		 if($user)
		 {
			 //update user
			 $sql="UPDATE waf_users SET pass='".md5($post['password'])."',status=1,editor=1 WHERE id=".mysql_real_escape_string($user['id']);
		 }else{
			  //install user
			 $sql="INSERT INTO waf_users(email,pass,status,editor) VALUES ('".mysql_real_escape_string($post['email'])."','".md5($post['password'])."',1,1)";		
		 }
		 mysql_query($sql,$this->connection);
		 //open session
		 $user=$this->getUserByEmail($post['email']);
		 $_SESSION['waf_user']=$user;
	 return true;
	 }
	 
	}
	private function getUserByEmail($email){
		 $sql1="SELECT * FROM waf_users WHERE email='".mysql_real_escape_string($email)."'";
		 $result=mysql_query($sql1,$this->connection);
		 if($result)return mysql_fetch_array($result);
		 else return false;
		 
	}
	private function create_config_file($post)
	{
	 $path=substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/")+1);
	 
	 $content="<?php


$"."config=Array('db_host'=>'".$post['dbhost']."',
			  'db_name'=>'".$post['dbname']."',
			  'db_user'=>'".$post['dbuser']."',
			  'db_pass'=>'".$post['dbpass']."',
				'web_root'=>'".$path."');
?>";
	 $f=fopen("inc/config.inc.php","a");
	 $r=fwrite($f, $content);
	 fclose($f);
	 if($r)return true;
	 else return false;
	}
	private function redirect2success($post)
	{
	 header("Location:installed.php");
	 exit();
	}
}
?>