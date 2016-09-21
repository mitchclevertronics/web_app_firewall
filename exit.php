<?php
/* 
 * Logout page
 * This product includes PHP software, freely available from <http://www.php.net/software/>
 * Author: Roman Shneer romanshneer@gmail.com
 */
session_start();
unset($_SESSION['waf_user']);
header('Location:login.php');
?>