<?php
	// Includes.
	include_once("./database/WutHappen.php");
	include_once("./libraries/Upload.class.php");
	include_once("./libraries/easyphpthumbnail.class.php");

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
	
	// GET EVENTS
	if(isset($_POST['eventType']))
	{
		$eventType = $_POST['eventType'];
		$owner = $_SESSION['user'];
		
		$wutHappen->dbConnect();
		$wutHappen->getEvents($eventType, $owner);
	}
	
	// FILE UPLOAD
	if(isset($_FILES['userFile']))
	{
		$file = $_FILES['userFile'];
		$user = $_SESSION['user'];
		$size = $_FILES['userFile']['size'];
		
		$wutHappen->dbConnect();
		$wutHappen->uploadImage($file, $user, $size);
	} 
	
	// GET IMAGES
	if(isset($_POST['getImages']))
	{
		$wutHappen->dbConnect();
		$wutHappen->getImages($_SESSION['user']);
	}
	
	// DELETE EVENT
	if(isset($_POST['deleteEventId'])){
		$wutHappen->dbConnect();
		$wutHappen->removeEvent($_SESSION['user'], $_POST['deleteEventId']);
	}
	
	// CREATE EVENT
	if(isset($_POST['image']) && 
		isset($_POST['header']) && 
		isset($_POST['date']) && 
		isset($_POST['time']) && 
		isset($_POST['location']) && 
		isset($_POST['content']))
	{
		$owner = $_SESSION['user'];
		$image = $_POST['image'];
		$header = $_POST['header'];
		$date = $_POST['date'];
		$time = $_POST['time'];
		$location = $_POST['location'];
		$content = $_POST['content'];
		
		$wutHappen->dbConnect();
		$wutHappen->addEvent($owner, $image, $header, $date, $time, $location, $content);
	}
	
	//if(!file_exists('uploads')) mkdir('uploads', 0755);
	//if(!file_exists('thumbs')) mkdir('thumbs', 0755);
	
	//array_map('unlink', glob("uploads/*"));
	//rmdir('uploads');

?>