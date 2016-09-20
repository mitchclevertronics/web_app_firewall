<?php
session_start();
unset($_SESSION['waf_user']);
header('Location:login.php');
?>