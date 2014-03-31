<?php
	require_once("./database/WutHappen.php");
	
	$wutHappen = new WutHappen();
	
	var_dump($wutHappen->getConnectionInfo());
	
	$wutHappen->dbConnect();
?>