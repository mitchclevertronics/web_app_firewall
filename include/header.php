<?php
/*
 * script for header.php
 * License: GNU
 * Copyright 2016 WebAppFirewall RomanShneer <romanshneer@gmail.com>
 */
$isEditor=(isset($WR)&&($WR->isEditor()))?true:((isset($WU)&&($WU->isEditor()))?true:false);
?>
<div class='header'>
    <ul class='menu'>
				<li><a href='<?php echo substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/")+1);?>'>Dashboard</a></li>
				<li><a href='settings.php'>Settings</a></li>
        <?php if($isEditor):?><li><a href='htaccess.php'>HTACCESS</a></li><?php endif;?>
				<li><a href='map.php'>Access Map</a></li>
				<li><a href='logs.php'>Bad Requests</a></li>
				<li><a href='blacklist.php'>BlackList</a></li>
				<li><a href='users.php'>Users</a></li>
				<li><a href='password.php'>Change Password</a></li>
				<li><a href='exit.php'>Exit</a></li>
    </ul>
</div>