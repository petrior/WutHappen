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
			$this->connectionInfo = getInfo(); // Save connection info to array from dbYhteys.php.
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
				echo(0);
			}
		}
		
		// Check if there is a user with specific email and password.
		// TODO Hash + salt!
		private function checkUser($user, $pwd) {
			$sql = "SELECT * FROM wh_users WHERE email='$user' AND
			password = '$pwd'";
			$STH = @$this->DBH->query($sql);
			if($STH->rowCount() > 0){
				$row = $STH->fetch();
				return $row["uid"];
			} else {
				return false;
		}
}
	}
?>