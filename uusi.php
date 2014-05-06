<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Luo uusi tapahtuma</title>
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="main.css">
	</head>
	<body>
		<?php
			require_once("./database/WutHappen.php");
			$wutHappen = new WutHappen();
			$wutHappen->SSLon();
			$wutHappen->startSession();
			
			if($_GET['logout'] == true)
			{
				$wutHappen->endSession();
			}
			
			if(!$_SESSION['logged'])
			{
				$wutHappen->redirect("./login.php");
			}
		?>
		<div class="navBar">
			<ul class="horizontalNav">
				<li class="dropdownEvent selected">
					<a href="#">Tapahtumat<img src="./images/arrowDown.png"></a>
					<ul>
						<li><a href="./main.php?eventType=own">Omat</a></li>
						<li><a href="./main.php?eventType=others">Muiden</a></li>
						<li><a href="./main.php?eventType=past">Menneet</a></li>
						<li class="selected"><a href="./uusi.php">Uusi</a></li>
					</ul>
				</li>
				<li><a href="#">Kaverit</a></li>
				<li class="dropdownEventRight floatRight">
					<a href="#">User</a>
					<ul>
						<li><a href="<?php echo($_SERVER['PHP_SELF'] . "?logout=true"); ?>">Kirjaudu ulos</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="container">
			<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
				<input type="text"></input>
			</form>
		</div>
		<script type="text/javascript" src="main.js"></script>
	</body>
</html>