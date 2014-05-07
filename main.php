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
				<li class="dropdownEvent selected">
					<a href="#">Tapahtumat<img src="./images/arrowDown.png"></a>
					<ul>
						<li <?php if($_GET['eventType'] == "own") echo 'class="selected"'; ?>><a href="<?php echo($_SERVER['PHP_SELF'] . "?eventType=own"); ?>">Omat</a></li>
						<li <?php if($_GET['eventType'] == "others") echo 'class="selected"'; ?>><a href="<?php echo($_SERVER['PHP_SELF'] . "?eventType=others"); ?>">Muiden</a></li>
						<li <?php if($_GET['eventType'] == "past") echo 'class="selected"'; ?>><a href="<?php echo($_SERVER['PHP_SELF'] . "?eventType=past"); ?>">Menneet</a></li>
						<li><a href="./uusi.php">Uusi</a></li>
					</ul>
				</li>
				<li class="dropdownEvent">
					<a href="#">Kaverit<img src="./images/arrowDown.png"></a>
					<ul>
						<li><a href=".\lisaakaveri.php">Lisää kaveri</a></li>
						<li><a href="#">Kaverilista</a></li>
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
			<?php 
				if(isset($_GET['eventType']))
					$eventType = $_GET['eventType'];
				else
					$eventType = "own";
					
				if($eventType == "own")
				{
					echo("<div class='event'><p class='eventOwner'>Tapahtumat \ Omat tapahtumat</p></div>");
				}
				else if($eventType == "others")
				{
					echo("<div class='event'><p class='eventOwner'>Tapahtumat \ Muiden tapahtumat</p></div>");
				}
				else
				{
					echo("<div class='event'><p class='eventOwner'>Tapahtumat \ Menneet tapahtumat</p></div>");
				}
				
				$owner = $_SESSION['user'];
		
				$wutHappen->dbConnect();
				$events = $wutHappen->getEvents($eventType, $owner);
				
				if(count($events) == 0)
					echo("<div class='event'><p class='dateText'>Ei tapahtumia</p></div>");
				else
				{
					foreach($events as $key=>$value): ?>
						<div class="event">
							<?php if($events[$key]->image != "false") echo('<div class="helperContainer"><img class="eventImage" src="' . $events[$key]->image . '" onclick="showContent(this);"></div>'); ?>
							<div class="helperContainer">
								<h3 class="eventHeader" onclick="showContent(this);"><?php echo($events[$key]->header); ?></h3>
								<p class="dateText"><?php
									$date = new DateTime($events[$key]->date);
									$time = new DateTime($events[$key]->time);
									echo(date_format($date, 'd.m.Y') . " Klo. " . date_format($time, 'H:i'));
								?></p>
								<p class="dateText"><?php echo($events[$key]->location); ?></p>
							</div>
							<div class="eventContent"><p><?php echo(nl2br($events[$key]->content)); ?></p></div>
							<div class="attendance">
								<p class="total"><?php echo($events[$key]->total); ?></p>
								<p class="friends"><?php echo($events[$key]->friends); ?></p>
								<?php
									if($eventType == "own")
									{
										echo('<a href="./muokkaus.php?id=' . $events[$key]->eid . '"><i class="fa fa-pencil fa-2x"></i></a>');
										echo('<i class="fa fa-trash-o fa-2x" onclick="deleteEvent(' . $events[$key]->eid . ')"></i>');
									}
								?>
								<i class="fa fa-group fa-2x"></i>								
							</div>
							<p class="eventOwner"><?php 
								$date = new DateTime($events[$key]->VST);
								echo("<span style='display:table-row; white-space:nowrap;'>" . $events[$key]->name . " - " . date_format($date, 'd.m.Y H:i:s') . "</span>");
							?></p>
						</div>
					<?php endforeach;
				}
			?>
		</div>
		<script type="text/javascript" src="main.js"></script>
	</body>
</html>