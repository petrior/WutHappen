<?php
	// Includes.
	require_once("./database/dbYhteys.php");
	
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
		
		// Just for testing... not really needed.
		public function getConnectionInfo()
		{
			return $this->connectionInfo;
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
		public function register($user, $pwd)
		{
			// For email.
			$regExp1 = '/^[a-z0-9\+\-_]+(\.[a-z0-9\+\-_]+)*@[a-z0-9\-]+(\.[a-z0-9\-]+)*\.[a-z]{2,6}$/i';
			// For password.
			$regExp2 = '/[A-Z]+/';
			$regExp3 = '/[0-9]+/';
			$regExp4 = '/.{8,}/';
			
			// Check email.
			if(preg_match($regExp1, $user) == 0)
			{
				echo("Email ei kelpaa!");
				exit;
			}
			
			// Check password.
			if(preg_match($regExp2, $pwd) == 0 || preg_match($regExp3, $pwd) == 0 || preg_match($regExp4, $pwd) == 0)
			{
				echo("Salasana ei kelpaa!");
				exit;
			}
			
			// Add salt.
			$pwd += $this->salt;
			
			// Hash the password.
			$hashedPwd = hash('sha256', $pwd);
			
			$sql = "SELECT email FROM wh_users WHERE email='$user';";
			$STH = @$this->DBH->query($sql);
			if($STH->rowCount() == 0)
			{
				$sql = "INSERT INTO wh_users(admin, password, email) VALUES(
					0,
					'$hashedPwd',
					'$user'
				);";
				$STH = @$this->DBH->query($sql);
			}
			else {
				echo("Sähköpostiosoite on jo käytössä.");
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
	}
?>