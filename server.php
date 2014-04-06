<?php
	// Includes.
	include_once("./database/WutHappen.php");

	$wutHappen = new WutHappen();
	$wutHappen->startSession();
	
	// LOGIN
	if(isset($_POST['lEmail']) && isset($_POST['lPwd']))
	{
		$email = $_POST['lEmail'];
		$pwd = $_POST['lPwd'];
		
		$wutHappen->dbConnect();
		$wutHappen->login($email, $pwd);
	}
	
	// LOGOUT
	if(isset($_POST['logout']))
	{
		$wutHappen->endSession();
	}
	
	// REGISTER
	if(isset($_POST['rEmail']) && isset($_POST['rPwd']) && isset($_POST['rName']))
	{
		$email = $_POST['rEmail'];
		$pwd = $_POST['rPwd'];
		$name = $_POST['rName'];
		
		$wutHappen->dbConnect();
		$wutHappen->register($email, $pwd, $name);
	}
?>