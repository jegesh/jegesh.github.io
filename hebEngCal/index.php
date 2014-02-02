<?php

include "hebEngCal.php";

?>
<!DOCTYPE html>
<html>
	<head>
		<link href="calendar.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<h1>Live Demo for Hebrew/English Calendar of Events</h1>
		<?php
			$myCal = new HeEnEventCal();
			$myCal->tableName="posts";
			$myCal->dateField="date";
			$myCal->linkIdField="ArticleID";
			$myCal->dateFormat='Y-m-d';
			$myCal->linkURL="www.avakshech.org/workshops.php?id=";
			$dbArray = array("mysql1089.servage.net","avakshech","avakshech","SwordFish2000");
			$myCal->setDbParams($dbArray);
			$myCal->printCal();
		?>
	</body>
</html>