<?php
/*
 * script for user logout
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
session_start();
unset($_SESSION['waf_user']);
header('Location:login.php');
?>