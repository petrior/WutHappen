<?php
	// Includes.
	require_once("./database/dbYhteys.php");
	
	// WutHappen class has all the server side functionality of the app.
	class WutHappen
	{
		// Variables.
		private $connectionInfo; // Array holding database user, pass, etc..
		private $DBH; // Database handler.
		
		// Constructor.
		public function __construct()
		{
			$this->connectionInfo = getInfo(); // Save connection info to array from dbYhteys.php.
		}
		
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
	}
?>