<?php
/*
 * script for head html
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
?>
<title>Web.App.Firewall.</title>

<?php if(isset($is_mobile)&&($is_mobile)):?>
<link rel="stylesheet" type="text/css" href="assets/css/style_mobile.css">
<?php else:?>
<link rel="stylesheet" type="text/css" href="assets/css/style.css">
<?php endif;?>
<link rel="stylesheet" type="text/css" href="assets/css/jquery-ui.css">
<script type="text/javascript" src="assets/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="assets/js/jquery-ui.min.js"></script>