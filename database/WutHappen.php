<?php
	// Includes.
	require_once("./database/dbYhteys.php");
	require_once("./libraries/htmlpurifier-4.6.0/library/HTMLPurifier.auto.php");
	
	// WutHappen class has all the server side functionality of the app.
	class WutHappen
	{
		// Variables.
		private $connectionInfo; // Array holding database user, pass, etc..
		private $DBH; // Database handler.
		private $salt = "AEFjidakld1254239rtlöäe234890awklej"; // Salt for passwords. Yummy.
		
		// Constructor.
		public function __construct()
		{
			$this->connectionInfo = getInfo(); // Save connection info from dbYhteys.php to an array.
		}
		
		// Connect to the database.
		public function dbConnect()
		{	
			try {
				// Database handler is a PDO-object.
				$this->DBH = new PDO('mysql:host=' . $this->connectionInfo["host"] . ';dbname=' . $this->connectionInfo["dbname"], $this->connectionInfo["user"], $this->connectionInfo["pass"]);
				// Setup handler to use error messages and PDOExceptions.
				$this->DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				// We want to use utf-8 encoding.
				$this->DBH->query("SET NAMES utf8;");
			}
			// If connection fails, save error message to a log file.
			catch(PDOException $e) {
				echo "Could not connect to database.";
				file_put_contents('./loki/PDOErrors.txt', $e->getMessage() . "\n", FILE_APPEND);
			}
		}
		
		// Start session.
		public function startSession()
		{
			session_start();
		}
		
		public function endSession()
		{
			session_destroy();
			unset($_SESSION['logged']);
			unset($_SESSION['user']);
		}
		
		// Register user.
		public function register($user, $pwd, $name)
		{	
			// Check email.
			if(!$this->validateEmail($user))
			{
				echo("Sähköposti ei kelpaa!");
				exit;
			}
			
			// Check password.
			if(!$hashedPwd = $this->validatePassword($pwd))
			{
				echo("Salasana ei kelpaa.");
				exit;
			}
			
			// Check name.
			if(!$this->validateName($name))
			{
				echo("Nimi ei kelpaa!");
				exit;
			}
			
			$sql = "SELECT email FROM wh_users WHERE email='$user';";
			$STH = @$this->DBH->query($sql);
			if($STH->rowCount() == 0)
			{
				$sql = "INSERT INTO wh_users(email, password, userlevel, name) VALUES(
					:user,
					:hashedPwd,
					0,
					:name
				);";
				$STH = @$this->DBH->prepare($sql);
				if($STH->execute(array('user' => $user, 'hashedPwd' => $hashedPwd, 'name' => $name)))
					echo("Rekisteröinti onnistui.");
				else
					echo("Tapahtui virhe.");
			}
			else
				echo("Sähköpostiosoite on jo käytössä.");
		}
		
		private function validateEmail($user)
		{
			// For email.
			$regExp1 = '/^[a-z0-9\+\-_]+(\.[a-z0-9\+\-_]+)*@[a-z0-9\-]+(\.[a-z0-9\-]+)*\.[a-z]{2,6}$/i';
			// Check email.
			if(preg_match($regExp1, $user) == 0)
				return false;
			else
				return true;
		}
		
		private function validatePassword($pwd)
		{
			// For password.
			$regExp2 = '/[A-Z]+/';
			$regExp3 = '/[0-9]+/';
			$regExp4 = '/.{8,}/';
			// Check password.
			if(preg_match($regExp2, $pwd) == 0 || preg_match($regExp3, $pwd) == 0 || preg_match($regExp4, $pwd) == 0)
				return false;
			else {
				// Add salt.
				$pwd += $this->salt;
				
				// Hash the password.
				$hashedPwd = hash('sha256', $pwd);
				return $hashedPwd;
			}
		}
		
		private function validateName($name)
		{
			// For name.
			$regExp5 = "/^[\s,-.'\pL]+$/u";
			// Check name.
			if(preg_match($regExp5, $name) == 0)
				return false;
			else
				return true;
		}
		
		public function generateGuestParameter()
		{
			mt_srand((double)microtime()*1000000);
			$token = mt_rand(1, mt_getrandmax());
			
			$uid = uniqid(md5($token), true);
			if($uid != false && $uid != '' && $uid != NULL)
			{
				$out = sha1($uid);
				return $out;
			}
			else {
				return false;
			}
		}
		
		// Login
		public function login($user, $pwd)
		{	
			// checkUser() returns user id from database if email and password are correct.
			// if wrong email or pass, checkUser() returns false.
			if($userId = $this->checkUser($user, $pwd))
			{
				$_SESSION['logged'] = true;
				$_SESSION['user'] = $userId;
				echo(1);
			}
			else {
				echo("Käyttäjätunnus tai salasana virheellinen.");
			}
		}
		
		// Check if there is a user with specific email and password.
		private function checkUser($user, $pwd)
		{
			// Add salt to password.
			$pwd += $this->salt;
			
			// Hash the password.
			$hashedPwd = hash('sha256', $pwd);
			
			$sql = "SELECT * FROM wh_users WHERE email='$user' AND
			password = '$hashedPwd'";
			$STH = @$this->DBH->query($sql);
			if($STH->rowCount() > 0){
				$row = $STH->fetch();
				return $row["uid"];
			} else {
				return false;
			}
		}
		
		// Force SSL
		public function SSLon()
		{
			if($_SERVER['HTTPS'] != 'on')
			{
				$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				if(!headers_sent())
				{
					header('Location: ' . $url);
					exit;
				} else {
					echo '<script type="text/javascript">';
					echo 'window.location.href="'.$url.'";';
					echo '</script>';
					echo '<noscript>';
					echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
					echo '</noscript>'; exit;
				}
			}
		}
		
		public function addEvent($owner, $image, $content, $date)
		{
			// Date validation
			$format = "Y-m-d H:i:s";
			if(!($d = DateTime::createFromFormat($format, $date)))
			{
				echo("Päivämäärä virheellinen!");
				exit;
			}
			
			// Content validation.
			// Default settings for HTMLPurifier.
			$config = HTMLPurifier_Config::createDefault();
			// Create new HTMLPurifier with the settings.
			$purifier = new HTMLPurifier($config);
			// Clean the content.
			$content = $purifier->purify($content);
			
			// Event is valid 2 weeks after the event.
			$vet = $d;
			$vet->add(new DateInterval('P13D'));
			$VET = $vet->format('Y-m-d H:i:s');
			
			// Change $d to string.
			$d = $d->format('Y-m-d H:i:s');
			
			$sql = "INSERT INTO wh_events(ownerid, image, content, date, vst, vet) VALUES(
				:owner,
				:image,
				:content,
				:date,
				CURRENT_TIMESTAMP,
				:vet
			);";
			
			$STH = @$this->DBH->prepare($sql);
			$STH->execute(array('owner' => $owner, 'image' => $image, 'content' => $content, 'date' => $d, 'vet' => $VET));
		}
		
		public function updateEvent($owner, $image, $content, $date, $eventid)
		{
			// Date validation
			$format = "Y-m-d H:i:s";
			if(!($d = DateTime::createFromFormat($format, $date)))
			{
				echo("Päivämäärä virheellinen!");
				exit;
			}
			
			// Content validation.
			// Default settings for HTMLPurifier.
			$config = HTMLPurifier_Config::createDefault();
			// Create new HTMLPurifier with the settings.
			$purifier = new HTMLPurifier($config);
			// Clean the content.
			$content = $purifier->purify($content);
			
			// Event is valid 2 weeks after the event.
			$vet = $d;
			$vet->add(new DateInterval('P13D'));
			$VET = $vet->format('Y-m-d H:i:s');
			
			// Change $d to string.
			$d = $d->format('Y-m-d H:i:s');
			
			/* UPDATE OLD ROW */
			$sql = "UPDATE wh_events SET VET=CURRENT_TIMESTAMP WHERE eid=:eventid AND ownerid=:owner;";
			$STH = @$this->DBH->prepare($sql);
			$STH->execute(array('eventid' => $eventid, 'owner' => $owner));
			if($STH->rowCount() == 1)
			{
				/* CREATE NEW ROW */
				$sql = "INSERT INTO wh_events(ownerid, image, content, date, VST, VET) VALUES(
					:owner,
					:image,
					:content,
					:date,
					CURRENT_TIMESTAMP,
					:vet
				);";
				
				$STH = @$this->DBH->prepare($sql);
				if($STH->execute(array('owner' => $owner, 'image' => $image, 'content' => $content, 'date' => $d, 'vet' => $VET)))
					echo("Muokkaus onnistui!");
				else
					echo("Tapahtui virhe!");
			}
			else {
				echo("Tapahtui virhe!");
			}
	
		}
		
		public function removeEvent($owner, $eventid)
		{
			/* UPDATE OLD ROW */
			$sql = "UPDATE wh_events SET VET=CURRENT_TIMESTAMP WHERE eid=:eventid AND ownerid=:owner;";
			$STH = @$this->DBH->prepare($sql);
			if($STH->execute(array('eventid' => $eventid, 'owner' => $owner)))
				echo("Poisto onnistui.");
			else echo("Tapahtui virhe.");
		}
		
		public function updateProfile($user, $pwd, $name, $address, $avatar)
		{	
			// Check password.
			if(!$hashedPwd = $this->validatePassword($pwd))
			{
				echo("Salasana ei kelpaa.");
				exit;
			}
			
			// Check name.
			if(!$this->validateName($name))
			{
				echo("Nimi ei kelpaa!");
				exit;
			}
			
			if($address != NULL)
			{
				// Address validation.
				// Default settings for HTMLPurifier.
				$config = HTMLPurifier_Config::createDefault();
				// Create new HTMLPurifier with the settings.
				$purifier = new HTMLPurifier($config);
				// Clean the content.
				$address = $purifier->purify($address);
			}
			
			$sql = "UPDATE wh_users SET password=:password, name=:name, address=:address, avatar=:avatar WHERE uid=:user;";
			$STH = @$this->DBH->prepare($sql);
			if($STH->execute(array('password' => $hashedPwd, 'name' => $name, 'address' => $address, 'avatar' => $avatar, 'user' => $user)))
				echo("Päivitys onnistui.");
			else echo("Tapahtui virhe.");
		}
	}
?>