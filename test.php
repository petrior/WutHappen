<?php
	require_once("./database/WutHappen.php");
	
	$wutHappen = new WutHappen();
	
	var_dump($wutHappen->getConnectionInfo());
	
	$wutHappen->dbConnect();
	
	$wutHappen->startSession();
	$wutHappen->login("petri.raut@gmail.com", "salasana");
	$wutHappen->endSession();
?>