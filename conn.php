<?php
		$dbhost1	=	"localhost";
		$dbname1	=	"members";
		$dbuser1	=	"root";
		$dbpass1	=	"";
$remote_machine=mysql_connect ($dbhost1,$dbuser1,$dbpass1) or die (mysql_error($remote_machine)."Machine Connection Failure");
$DB1=mysql_select_db ($dbname1,$remote_machine) or die (mysql_error($remote_machine)."Database Selection Failure");
?>