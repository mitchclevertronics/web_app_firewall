<?php
/*
 * script for user management (backend library)
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
/* 
 * WAF User managment
 */
Class WafUser{
	public $error='';
	public $web_root;
	
	function __construct(){
		
		$config=DB::get_config();
		$this->web_root=$config['web_root'];
		$this->db=new DB($config['db_host'],$config['db_name'],$config['db_user'],$config['db_pass']);
    $this->waf_bf=$this->get_setting('waf_bf','0.3');
	}
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
	 * get User per email\password and save to sessin redirect to dashboard
	 * @param string $email
	 * @param string $pass
	 */
	public function auth_user($email,$pass){
	 $user=$this->db->ROW_Q("SELECT * FROM waf_users WHERE email='".$this->db->Q($email)."' AND status=1");
	 
		if($user)
		{
		  $t=  microtime(true);
			$diff=($t-$user['bf']);
			
			$this->db->QUERY("UPDATE waf_users SET bf=".$t." WHERE id=".$this->db->Q($user['id'])); 
			
			if($diff<$this->waf_bf)
			{
			
			 $this->error='Wrong User or Password!';
			 return null;
			}elseif($user['pass']!=md5(trim($pass))){
			 
			 $this->error='Wrong User or Password!';
			 return null;
			}else{
			 
			 $_SESSION['waf_user']=$user;
			header("Location:".$this->web_root);
			exit();
			}
		 #$sql="UPDATE waf_users SET bf=".$t." WHERE email='".$this->db->Q($email)."'"; 
		 
			
		}else{
			$this->error='Wrong User or Password!';
		}
		
	}
	
	/*
	 * check user session and redirect if not exists
	 * if get param $chk_editor=true - check if user have editor permission
	 * @param bool $chk_editor - status check editor or not
	 * return void
	 */
	public function check_user_session($chk_editor=false)
	{
	
		if(!isset($_SESSION['waf_user']))
		{
			header("Location:".$this->web_root."login.php");
			exit();
		}
		if($chk_editor)
		{
			if($_SESSION['waf_user']['editor']!=1)
			{
				header("Location:".$this->web_root."login.php");
				exit();
			}	
		}
	}
	/*
	 * Update user password by id
	 * @param int $id - UserID
	 * @param string $pass - user new password
	 * @return void
	 */
	public function change_password($id,$pass)
	{
		$sql="UPDATE waf_users SET pass='".md5($pass)."' WHERE id=".$this->db->Q($id);
    $this->db->QUERY($sql);
	}
	/*
	 * Run Change Password Interface
	 * (event function for check old password, if new confirmed and call to save)
	 * return void
	 */
	public function run_chg_pass_if()
	{
		$msg='';
		if(isset($_POST['old_pass']))
		{
			if(md5($_POST['old_pass'])==$_SESSION['waf_user']['pass'])
				{
					if($_POST['pass']!=$_POST['pass1'])
					{
						$msg='Passwords not equal! Try again.';
					}else{
					$this->change_password($_SESSION['waf_user']['id'],$_POST['pass']);
					$msg='Password successfully changed';
					}
				}else{
				$msg='Old Password wrong! Password not changed';	
				}
		}
		$this->error=$msg;
	}
	
	/*
	 * Get All Users for management
	 * @return array $users
	 */
	public function get_users()
	{
		$sql="SELECT * FROM waf_users WHERE 1=1";
    $result=$this->db->LIST_Q($sql);
		return $result;
	}
	
	/*
	 * Get One User per id
	 * @param int $id UserId
	 * return array $user
	 */
	public function get_user($id)
	{
		if($id>0)
		{	
		$sql="SELECT * FROM waf_users WHERE id=".$this->db->Q($id);
    $result=$this->db->ROW_Q($sql);
		}else{
			$result=Array('id'=>0,'email'=>'','editor'=>0,'status'=>1);
		}
		return $result;
	}
	
	/*
	 * Insert\Update User (if id=0 - insert)
	 * @param array $_GET
	 * @return void
	 */
	public function save_user($u)
	{
	
		if($u['id'])
		{
			$sql="UPDATE waf_users SET email='".$this->db->Q($u['email'],1)."', ";
															if(!empty($u['pass']))$sql.="pass='".md5($u['pass'])."', ";
															$sql.= "editor=".(isset($u['editor'])?1:0).", "
															. "status=".(isset($u['status'])?1:0)." "
															. "WHERE id=".$this->db->Q($u['id']);
		}else{
			$sql="INSERT INTO waf_users (email,pass,editor,status) VALUES ('".$this->db->Q($u['email'],1)."','".md5($u['pass'])."',".(isset($u['editor'])?1:0).",".(isset($u['status'])?1:0).")";
		}
		$this->db->QUERY($sql);
	}
	public function isEditor(){
	 if($_SESSION['waf_user']['editor']==1)return true;
	 else return false;
	}
	
	public function remind_password($email){
	 $sql="SELECT * FROM waf_users WHERE email='".$this->db->Q($email,1)."'";
    $result=$this->db->ROW_Q($sql);
		if($result)
		{
		 $mdkey=md5(rand(10000,time()));
		 
		 
		 $sql1="UPDATE waf_users SET rmn_pass='".$mdkey."' WHERE id=".$this->db->Q($result['id']);
		 $this->db->QUERY($sql1);
		
		 $link=(isset($_SERVER['HTTPS'] )?'https':'http')."://".$_SERVER['SERVER_NAME'].$this->web_root."reset_password.php?key=".$mdkey;
		 $msg="Some body try reset your password."
					."If you forget password "
					."<a href='".$link."'>click link</a>"
					."W.A.F. System";
		# die($msg);
		 mail($result['email'],$subject,$msg);
		 header("Location:?sended=1");
		}else{
		 return 'Guru you never know';
		}
	}
	public function auth_user_by_rmn_pass($key)
	{
	 $sql="SELECT * FROM waf_users WHERE rmn_pass='".$this->db->Q($key,1)."'";
    $result=$this->db->ROW_Q($sql);
		return $result;
		
	}
	public function run_chg_pass_if4reset($user)
	{
		$msg='';
	
		if($_POST['pass']!=$_POST['pass1'])
		{
			$msg='Passwords not equal! Try again.';
		}else{
		$this->change_password($user['id'],$_POST['pass']);
		
		$this->db->QUERY("UPDATE waf_users SET rmn_pass='' WHERE id=".$this->db->Q($user['id']));
		header("Location:?pass_changed=true");
		exit();
		#$msg='Password successfully changed';
		}
				
		
		$this->error=$msg;
	}
}
