<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<meta name="viewport" content="width=device-width" />
		<title>WutHappen - Login</title>
		<link rel="stylesheet" type="text/css" href="login.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	</head>
	<body>
		<?php
			require_once("./database/WutHappen.php");
			$wutHappen = new WutHappen();
			$wutHappen->SSLon();
		?>
		
		<div id="container">
			<div class="loginDiv">
				<img src="./images/wuthlogo.png"></img>
				<form id="loginForm">
					<input name="lEmail" class="loginInput" type="text" placeholder="Sähköpostiosoite"></input><br>
					<input name="lPwd" class="loginInput" type="password" placeholder="Salasana"></input><br>
					<p id="lError" class="error"></p>
					<input name="lSbmt" class="loginBtn" type="submit" value="Kirjaudu"></input>
				</form>
				<a id="showRegister" href="#register">Luo uusi tili</a>
				<a class="linkLeftBorder" id="showInfo" href="#info">Lisätietoja</a>
			</div>
			<div class="loginDiv" id="register">
				<a class="close" onclick="closeDiv(this)" href="#">sulje</a>
				<form id="registerForm">
					<input name="rEmail" class="loginInput" type="text" placeholder="Sähköpostiosoite"></input><br>
					<input name="rPwd1" class="loginInput" type="password" placeholder="Salasana"></input><br>
					<input name="rPwd2" class="loginInput" type="password" placeholder="Salasana uudelleen"></input><br>
					<input name="rName" class="loginInput" type="text" placeholder="Nimi"></input><br>
					<p id="rError" class="error"></p>
					<input name="rSbmt" class="loginBtn" type="submit" value="Luo tili"></input>
				</form>
			</div>
			<div class="loginDiv" id="info">
				<a class="close" onclick="closeDiv(this)" href="#">sulje</a>
					<p>asdfasfdsfadf</p>
			</div>
		</div>
		<script src="WutHappen.js"></script>
	</body>
</html>