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
				header( 'Location: ./login.php' ) ;
			}
		?>
		<div id="navBar">
			<ul>
				<li><a href="#" id="logout">Logout</a></li>
			</ul>
		</div>
		<div id="container">
			
			<div class="event">
				<div class="eventBar">
					<div class="eventImage">
						<img class="eventImg" src="./images/yelp-logo1.jpg" onclick="showContent(this)"></img>
					</div>
					<div class="eventHeader">
						<h3 class="eventH3" onclick="showContent(this)">Testitapahtuma 1</h3>
						<p>12.4.2014</p>	
						<div class="eventContent">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi elementum nisl vitae ipsum pulvinar luctus. Aliquam bibendum convallis nunc, quis pellentesque ligula rhoncus non. Fusce interdum eget sem sit amet tempus. Nullam lacus risus, rhoncus et turpis at, interdum euismod nisl. Curabitur purus nisi, porta id malesuada non, aliquam et massa. Duis condimentum orci non ultricies commodo. Donec nec nulla et tortor egestas semper. Nam adipiscing commodo lacinia. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam condimentum nec erat at luctus. Vivamus vel turpis quis lectus pulvinar ornare. In ac semper nibh. Nam consequat consectetur odio, vitae volutpat erat scelerisque id. Nullam eget tincidunt nisl. Proin vitae feugiat velit, eu consequat leo.

								Cras fermentum luctus semper. Nullam consequat faucibus interdum. Quisque vel erat sed mauris suscipit iaculis sit amet porttitor dolor. In pellentesque nibh ut risus tristique, nec viverra ligula convallis. Sed facilisis blandit placerat. Etiam ac pretium lorem. Integer at quam eget nulla dapibus congue ac id quam. Vivamus aliquet urna ac nulla congue lacinia. Nam dictum iaculis felis, quis pellentesque quam ornare a. In adipiscing, nibh in rutrum semper, enim risus commodo sem, et ultrices orci nibh sed purus. Maecenas facilisis mattis elit bibendum tempus. Proin leo nulla, porta nec rhoncus in, fermentum ut eros.
							</p>
						</div>
						<div class="attendance">
							<p class="total">120</p>
							<p class="friends">12</p>
						</div>
					</div>
				</div>
			</div>
			
			<div class="event">
				<div class="eventBar">
					<div class="eventImage">
						<img class="eventImg" src="./images/wuthlogo.png" onclick="showContent(this)"></img>
					</div>
					<div class="eventHeader">
						<h3 class="eventH3" onclick="showContent(this)">Testitapahtuma 2</h3>
						<p>12.4.2014</p>	
						<div class="eventContent">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi elementum nisl vitae ipsum pulvinar luctus. Aliquam bibendum convallis nunc, quis pellentesque ligula rhoncus non. Fusce interdum eget sem sit amet tempus. Nullam lacus risus, rhoncus et turpis at, interdum euismod nisl. Curabitur purus nisi, porta id malesuada non, aliquam et massa. Duis condimentum orci non ultricies commodo. Donec nec nulla et tortor egestas semper. Nam adipiscing commodo lacinia. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam condimentum nec erat at luctus. Vivamus vel turpis quis lectus pulvinar ornare. In ac semper nibh. Nam consequat consectetur odio, vitae volutpat erat scelerisque id. Nullam eget tincidunt nisl. Proin vitae feugiat velit, eu consequat leo.

								Cras fermentum luctus semper. Nullam consequat faucibus interdum. Quisque vel erat sed mauris suscipit iaculis sit amet porttitor dolor. In pellentesque nibh ut risus tristique, nec viverra ligula convallis. Sed facilisis blandit placerat. Etiam ac pretium lorem. Integer at quam eget nulla dapibus congue ac id quam. Vivamus aliquet urna ac nulla congue lacinia. Nam dictum iaculis felis, quis pellentesque quam ornare a. In adipiscing, nibh in rutrum semper, enim risus commodo sem, et ultrices orci nibh sed purus. Maecenas facilisis mattis elit bibendum tempus. Proin leo nulla, porta nec rhoncus in, fermentum ut eros.
							</p>
						</div>
						<div class="attendance">
							<p class="total">120</p>
							<p class="friends">12</p>
						</div>
					</div>
				</div>
			</div>
			
			<div class="event">
				<div class="eventBar">
					<div class="eventImage">
						<img class="eventImg" src="./images/nappialas.png" onclick="showContent(this)"></img>
					</div>
					<div class="eventHeader">
						<h3 class="eventH3" onclick="showContent(this)">Testitapahtuma 3</h3>
						<p>12.4.2014</p>	
						<div class="eventContent">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi elementum nisl vitae ipsum pulvinar luctus. Aliquam bibendum convallis nunc, quis pellentesque ligula rhoncus non. Fusce interdum eget sem sit amet tempus. Nullam lacus risus, rhoncus et turpis at, interdum euismod nisl. Curabitur purus nisi, porta id malesuada non, aliquam et massa. Duis condimentum orci non ultricies commodo. Donec nec nulla et tortor egestas semper. Nam adipiscing commodo lacinia. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam condimentum nec erat at luctus. Vivamus vel turpis quis lectus pulvinar ornare. In ac semper nibh. Nam consequat consectetur odio, vitae volutpat erat scelerisque id. Nullam eget tincidunt nisl. Proin vitae feugiat velit, eu consequat leo.

								Cras fermentum luctus semper. Nullam consequat faucibus interdum. Quisque vel erat sed mauris suscipit iaculis sit amet porttitor dolor. In pellentesque nibh ut risus tristique, nec viverra ligula convallis. Sed facilisis blandit placerat. Etiam ac pretium lorem. Integer at quam eget nulla dapibus congue ac id quam. Vivamus aliquet urna ac nulla congue lacinia. Nam dictum iaculis felis, quis pellentesque quam ornare a. In adipiscing, nibh in rutrum semper, enim risus commodo sem, et ultrices orci nibh sed purus. Maecenas facilisis mattis elit bibendum tempus. Proin leo nulla, porta nec rhoncus in, fermentum ut eros.
							</p>
						</div>
						<div class="attendance">
							<p class="total">120</p>
							<p class="friends">12</p>
						</div>
					</div>
				</div>
			</div>
			
			<div class="event">
				<div class="eventBar">
					<div class="eventImage">
						<img class="eventImg" src="./images/database.jpg" onclick="showContent(this)"></img>
					</div>
					<div class="eventHeader">
						<h3 class="eventH3" onclick="showContent(this)">Testitapahtuma 3</h3>
						<p>12.4.2014</p>	
						<div class="eventContent">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi elementum nisl vitae ipsum pulvinar luctus. Aliquam bibendum convallis nunc, quis pellentesque ligula rhoncus non. Fusce interdum eget sem sit amet tempus. Nullam lacus risus, rhoncus et turpis at, interdum euismod nisl. Curabitur purus nisi, porta id malesuada non, aliquam et massa. Duis condimentum orci non ultricies commodo. Donec nec nulla et tortor egestas semper. Nam adipiscing commodo lacinia. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam condimentum nec erat at luctus. Vivamus vel turpis quis lectus pulvinar ornare. In ac semper nibh. Nam consequat consectetur odio, vitae volutpat erat scelerisque id. Nullam eget tincidunt nisl. Proin vitae feugiat velit, eu consequat leo.

								Cras fermentum luctus semper. Nullam consequat faucibus interdum. Quisque vel erat sed mauris suscipit iaculis sit amet porttitor dolor. In pellentesque nibh ut risus tristique, nec viverra ligula convallis. Sed facilisis blandit placerat. Etiam ac pretium lorem. Integer at quam eget nulla dapibus congue ac id quam. Vivamus aliquet urna ac nulla congue lacinia. Nam dictum iaculis felis, quis pellentesque quam ornare a. In adipiscing, nibh in rutrum semper, enim risus commodo sem, et ultrices orci nibh sed purus. Maecenas facilisis mattis elit bibendum tempus. Proin leo nulla, porta nec rhoncus in, fermentum ut eros.
							</p>
						</div>
						<div class="attendance">
							<p class="total">120</p>
							<p class="friends">12</p>
						</div>
					</div>
				</div>
			</div>
		
		</div>
		<script src="WutHappen.js"></script>
	</body>
</html>