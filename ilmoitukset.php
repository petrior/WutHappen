<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>WutHappen</title>
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="main.css">
	</head>
	<body>
		<?php
			require_once("./database/WutHappen.php");
			$wutHappen = new WutHappen();
			$wutHappen->SSLon();
			$wutHappen->startSession();
			$wutHappen->dbConnect();
			
			if($_GET['logout'] == true)
			{
				$wutHappen->endSession();
			}
			
			if(!$_SESSION['logged'])
			{
				$wutHappen->redirect("./login.php");
			}
			
			$messages = $wutHappen->getUserMessages($_SESSION['user']);
		?>
		<div class="navBar">
			<ul class="horizontalNav">
				<li class="dropdownEvent">
					<a href="#">Tapahtumat<img src="./images/arrowDown.png"></a>
					<ul>
						<li><a href="./main.php?eventType=own">Omat</a></li>
						<li><a href="./main.php?eventType=others">Muiden</a></li>
						<li><a href="./main.php?eventType=past">Menneet</a></li>
						<li><a href="./uusi.php">Uusi</a></li>
					</ul>
				</li>
				<li><a href="./kaverilista.php">Kaverilista</a></li>
				<?php 
					if(count($messages) > 0)
					{
						echo("<li class='selected'><a href='#' class='navIcon'><i class='fa fa-exclamation-circle'></i><span id='msgNumber'>" . count($messages) . "</span></a></li>");
					}
				?>
				<li class="dropdownEventRight floatRight">
					<a href="#">User</a>
					<ul>
						<li><a href="<?php echo($_SERVER['PHP_SELF'] . "?logout=true"); ?>">Kirjaudu ulos</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="container">
			<div class="event"><p class="eventOwner">Ilmoitukset</p></div>
			<div class="event">
				<table class="friendList">
					<?php foreach($messages as $key=>$value): ?>
					<tr><td><?php echo($messages[$key]->message); ?></td><td><button class="eventInputBtn" onclick="confirmFriendInvite(this, <?php echo($messages[$key]->uid . "," . $messages[$key]->umid); ?>)">Vahvista</button></td><td><button class="eventInputBtn">Hylkää</button></td></tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<script type="text/javascript" src="main.js"></script>
	</body>
</html>