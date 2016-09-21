<?php
/* 
 * Installation completed page
 * This product includes PHP software, freely available from <http://www.php.net/software/>
 * Author: Roman Shneer romanshneer@gmail.com
 */
session_start();

require_once "libs/db.inc.php";

require_once "libs/waf_report.class.php";

$WR=new WafReport;

?>
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
<head>
<?php require_once "include/head.php"; ?>
</head>
<body>
		<?php include_once 'include/header.php';?>      
		<h1 class='title'>Wellcome to W.A.F.</h1>
		
				<div class='box'>	
						<center>	
								<h5>Successfully installed!</h5>		
								<p><small>(For start fresh installation, remove file "libs/config.inc.php")</small></p>
						</center>
				</div>
</body>
</html>