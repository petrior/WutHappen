<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<meta name="viewport" content="width=device-width" />
		<title>WutHappen</title>
		<link rel="stylesheet" type="text/css" href="WutHappen.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	</head>
	<body>
		<?php
			require_once("./database/WutHappen.php");
			$wutHappen = new WutHappen();
			$wutHappen->SSLon();
			$wutHappen->startSession();
			
			if(!$_SESSION['logged'])
			{
				header('Location: ./login.php');
			}
		?>
		<div id="navBar">
			<ul>
				<li><a href="#" id="logout">Logout</a></li>
				<li><a href="#" id="own">Omat tapahtumat</a></li>
				<li><a href="#" id="invited">Kutsut</a></li>
				<li><a href="#" id="past">Menneet tapahtumat</a></li>
				<li><a href="#" id="newEvent">Luo tapahtuma</a></li>
			</ul>
		</div>
		<div id="container">
			<div id="events"></div>
			<div id="eventForm" class="eventForm">
				<input class="eventInputBtn" type="button" value="Valitse kuva" id="choosePicture"></input>
				<input class="eventInputBtn" type="button" id="loadPicture" value="Uusi kuva"></input>
				<div id="pictureContainer"></div>
				<div id="picturePicker">
					<form id="pictureForm" enctype="multipart/form-data">
						<button class="eventInputBtn" id="uploadFileBtn">Valitse tiedosto</button>
						<input class="uploadInput" type="file" name="userFile" id="uploadInput"></input>
						<input disabled="disabled" placeholder="Valitse tiedosto" type="text" class="eventInput fileName" id="fileName"></input>
						<button class="eventInputBtn" id="uploadImage">Lataa</button>
					</form>
				</div>
				<form id="contentForm">
					<img id="eventFormImg"></img>
					<input id="eHeader" class="eventInput" type="text" placeholder="Otsikko"></input>
					<input id="eDate" class="eventInput eventInputDate" type="text" placeholder="Päivämäärä"></input>
					<input id="eTime" class="eventInput eventInputTime" type="text" placeholder="Kello (00.00)"></input>
					<input id="eLocation" class="eventInput" type="text" placeholder="Paikka"></input>
					<textarea id="eContent" class="eventInputArea" placeholder="Kuvaus"></textarea>
					<button class="eventInputBtn" id="createEvent">Luo tapahtuma</button>
				</form>
			</div>
		</div>
		<script src="WutHappen.js"></script>
		<script type="text/javascript">
			// Do this when page loads...
			$.ajax({
				url: 'server.php',
				data: { 'eventType':'invited' },
				method: 'POST',
				dataType: 'json'
			}).done(function(data){
				createList(data);
			});
		</script>
	</body>
</html>