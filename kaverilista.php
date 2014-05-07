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
				<li class="dropdownEvent">
					<a href="#">Tapahtumat<img src="./images/arrowDown.png"></a>
					<ul>
						<li><a href="./main.php?eventType=own">Omat</a></li>
						<li><a href="./main.php?eventType=others">Muiden</a></li>
						<li><a href="./main.php?eventType=past">Menneet</a></li>
						<li><a href="./uusi.php">Uusi</a></li>
					</ul>
				</li>
				<li class="dropdownEvent selected">
					<a href="#">Kaverit<img src="./images/arrowDown.png"></a>
					<ul>
						<li class="selected"><a href=".\kaverilista.php">Kaverilista</a></li>
					</ul>
				</li>
				<li class="dropdownEventRight floatRight">
					<a href="#">User</a>
					<ul>
						<li><a href="<?php echo($_SERVER['PHP_SELF'] . "?logout=true"); ?>">Kirjaudu ulos</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="container">
			<div class="event"><p class="eventOwner">Kaverit \ Kaverilista</p></div>
			<div class="event">
				<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
					<input type="text" name="email" class="eventInput" placeholder="Kaverin sähköposti"></input>
					<input type="submit" class="createButton" value="Lisää"></input>
				</form>
				<table class="friendList">
					<tr><th>Nimi</th><th>Sähköposti</th></tr>
					<?php
					$wutHappen->dbConnect();
					$friends = $wutHappen->getFriends($_SESSION['user']);
					foreach($friends as $key=>$value): ?>
						<tr><td><?php echo($friends[$key]->name); ?></td><td><?php echo($friends[$key]->email); ?></td></tr>
					<?php endforeach; ?>
					<?php
						if(isset($_POST['email']))
						{
							$wutHappen->dbConnect();
							echo($wutHappen->inviteFriend($_POST['email'], $_SESSION['user']));
						}
					?>
				</table>
			</div>
		</div>
		<script type="text/javascript" src="main.js"></script>
	</body>
</html>