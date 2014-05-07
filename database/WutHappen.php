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
				echo(json_encode("Could not connect to database."));
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
			unset($_SESSION['logged']);
			unset($_SESSION['user']);
			session_destroy();
		}
		
		// Register user.
		public function register($user, $pwd, $name)
		{	
			// Check email.
			if(!$this->validateEmail($user))
			{
				echo(json_encode("Sähköposti ei kelpaa!"));
				exit;
			}
			
			// Check password.
			if(!$hashedPwd = $this->validatePassword($pwd))
			{
				echo(json_encode("Salasana ei kelpaa."));
				exit;
			}
			
			// Check name.
			if(!$this->validateName($name))
			{
				echo(json_encode("Nimi ei kelpaa!"));
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
					echo(json_encode(1));
				else
					echo(json_encode("Tapahtui virhe."));
			}
			else
				echo(json_encode("Sähköpostiosoite on jo käytössä."));
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
				echo(json_encode(1));
			}
			else {
				echo(json_encode("Käyttäjätunnus tai salasana virheellinen."));
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
		
		// Redirect to URL
		public function redirect($url)
		{
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
		
		// Force SSL
		public function SSLon()
		{
			if($_SERVER['HTTPS'] != 'on')
			{
				$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$this->redirect($url);
			}
		}
		
		public function addEvent($owner, $image, $header, $date, $time, $location, $content)
		{
			$time .= ".00";
			$dateTime = $date . " " . $time;
			// Date validation
			$format = "d.m.Y H.i.s";
			if(!($d = DateTime::createFromFormat($format, $dateTime)) || substr($time, 0, 2) > 23 || substr($time, 3, 2) > 59)
			{
				echo("Kellonaika ei kelpaa");
			}
			else
			{
			
				$format = "d.m.Y";
				$mysqlDate = DateTime::createFromFormat($format, $date);
				$mysqlDate = $mysqlDate->format("Y-m-d");
				
				$format = "H.i.s";
				$mysqlTime = DateTime::createFromFormat($format, $time);
				$mysqlTime = $mysqlTime->format("H:i:s");
				
				// Content validation.
				// Default settings for HTMLPurifier.
				$config = HTMLPurifier_Config::createDefault();
				// Create new HTMLPurifier with the settings.
				$purifier = new HTMLPurifier($config);
				// Clean the image.
				$image = $purifier->purify($image);
				// Clean the header.
				$header = $purifier->purify($header);
				// Clean the location.
				$location = $purifier->purify($location);
				// Clean the content.
				$content = $purifier->purify($content);
				
				// Event is valid 1 day after the event.
				$vet = $d;
				$vet->add(new DateInterval('P1D'));
				$VET = $vet->format('Y-m-d H:i:s');
				
				// Change $d to string.
				$d = $d->format('Y-m-d H:i:s');
				
				$sql = "INSERT INTO wh_events(ownerid, image, header, date, time, location, content, VST, VET) VALUES(
					:owner,
					:image,
					:header,
					:mysqlDate,
					:mysqlTime,
					:location,
					:content,
					CURRENT_TIMESTAMP,
					:vet
				);";
				
				$STH = @$this->DBH->prepare($sql);
				if($STH->execute(array('owner' => $owner, 
									'image' => $image,
									'header' => $header,
									'mysqlDate' => $mysqlDate,
									'mysqlTime' => $mysqlTime,
									'location' => $location,
									'content' => $content,
									'vet' => $VET)))
				echo("Lisäys onnistui");
				else echo("Tapahtui virhe.");
			}
		}
		
		public function updateEvent($owner, $image, $header, $date, $time, $location, $content, $eventid)
		{
			$time .= ".00";
			$dateTime = $date . " " . $time;
			// Date validation
			$format = "d.m.Y H.i.s";
			if(!($d = DateTime::createFromFormat($format, $dateTime)))
			{
				echo("Päivämäärä virheellinen!");
				exit;
			}
			
			$format = "d.m.Y";
			$mysqlDate = DateTime::createFromFormat($format, $date);
			$mysqlDate = $mysqlDate->format("Y-m-d");
			
			$format = "H.i.s";
			$mysqlTime = DateTime::createFromFormat($format, $time);
			$mysqlTime = $mysqlTime->format("H:i:s");
			
			// Content validation.
			// Default settings for HTMLPurifier.
			$config = HTMLPurifier_Config::createDefault();
			// Create new HTMLPurifier with the settings.
			$purifier = new HTMLPurifier($config);
			// Clean the content.
			$content = $purifier->purify($content);
			
			// Event is valid 1 day after the event.
			$vet = $d;
			$vet->add(new DateInterval('P1D'));
			$VET = $vet->format('Y-m-d H:i:s');
			
			// Change $d to string.
			$d = $d->format('Y-m-d H:i:s');
			
			/* UPDATE OLD ROW */
			/*$sql = "UPDATE wh_events SET VET=CURRENT_TIMESTAMP WHERE eid=:eventid AND ownerid=:owner;";
			$STH = @$this->DBH->prepare($sql);
			$STH->execute(array('eventid' => $eventid, 'owner' => $owner));
			if($STH->rowCount() == 1)

			{*/
				/* CREATE NEW ROW */



				$sql = "UPDATE wh_events 
				SET
					image=:image,
					header=:header,
					date=:date,
					time=:time,
					location=:location,
					content=:content,

					VST=CURRENT_TIMESTAMP,


					VET=:vet
				WHERE 
					eid=:eventid AND ownerid=:owner;";
				
				$STH = @$this->DBH->prepare($sql);
				if($STH->execute(array('owner' => $owner, 'image' => $image, 'header' => $header, 'date' => $mysqlDate, 'time' => $mysqlTime, 'location' => $location, 'content' => $content, 'vet' => $VET, 'eventid' => $eventid)))

				{
					$this->redirect("./main.php?eventType=own");
				}
				else
					echo("Tapahtui virhe!");

			/*}
			else {
				echo("Tapahtui virhe!");

			}*/
	
		}
		
		public function removeEvent($owner, $eventid)
		{

			$sql = "DELETE FROM wh_events WHERE eid=:eventid AND ownerid=:owner;";
			$STH = @$this->DBH->prepare($sql);
			if($STH->execute(array('eventid' => $eventid, 'owner' => $owner)))
				echo("Poisto onnistui.");
			else echo("Tapahtui virhe.");
			echo("WutHappen.php");
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
		
		// Get events.
		public function getEvents($eventType, $owner)
		{
			switch($eventType)
			{
				case "own":
					$sql = "SELECT 
							E.eid,
							E.image,
							E.header,
							E.date,
							E.time,
							E.location,
							E.content, 
							E.VST, 
							U.name, 
							(SELECT 
								COUNT(*)
							FROM 
								wh_friends F, 
								wh_users UU, 
								wh_event_invites EI 
							WHERE 
								  F.person1 = :owner 
								  AND F.person2 = UU.uid
								  AND F.VET IS NULL
								  AND EI.user = F.person2 
								  AND EI.event = E.eid 
								  AND EI.VET < CURRENT_TIMESTAMP) friends,
							(SELECT
								COUNT(*)
							FROM
								wh_event_invites EI
							WHERE
								EI.event = E.eid
								AND EI.VET < CURRENT_TIMESTAMP) total
						FROM 
							wh_events E, 
							wh_users U 
						WHERE 
							E.ownerid = :owner 
							AND E.ownerid = U.uid 
							AND E.VET > CURRENT_TIMESTAMP
						ORDER BY
							E.date ASC,
							E.time ASC;";
					break;
				case "others":
					$sql = "SELECT 
							E.eid,
							E.image,
							E.header,
							E.date,
							E.time,
							E.location,
							E.content, 
							E.VST, 
							U.name, 
								(SELECT 
									COUNT(*)
								FROM 
									wh_friends F, 
									wh_users UU, 
									wh_event_invites EI 
								WHERE 
									  F.person1 = :owner 
									  AND F.person2 = UU.uid
									  AND F.VET IS NULL
									  AND EI.user = F.person2 
									  AND EI.event = E.eid 
									  AND EI.VET < CURRENT_TIMESTAMP) friends,
								(SELECT
									COUNT(*)
								FROM
									wh_event_invites EI
								WHERE
									EI.event = E.eid
									AND EI.VET < CURRENT_TIMESTAMP) total
							FROM 
								wh_events E, 
								wh_event_invites I, 
								wh_users U
							WHERE 
								I.user = :owner 
								AND U.uid = E.ownerid AND I.event = E.eid AND E.VET > CURRENT_TIMESTAMP 
							ORDER BY 
								E.date ASC,
								E.time ASC;";
					break;
				case "past":
					$sql = "(SELECT 
							E.eid,
							E.image,
							E.header,
							E.date,
							E.time,
							E.location,
							E.content, 
							E.VST, 
							U.name, 
								(SELECT 
									COUNT(*)
								FROM 
									wh_friends F, 
									wh_users UU, 
									wh_event_invites EI 
								WHERE 
									  F.person1 = :owner 
									  AND F.person2 = UU.uid
									  AND F.VET IS NULL
									  AND EI.user = F.person2 
									  AND EI.event = E.eid 
									  AND EI.VET < CURRENT_TIMESTAMP) friends,
								(SELECT
									COUNT(*)
								FROM
									wh_event_invites EI
								WHERE
									EI.event = E.eid
									AND EI.VET < CURRENT_TIMESTAMP) total 
							FROM 
								wh_events E, 
								wh_users U 
							WHERE 
								E.ownerid =:owner 
								AND E.ownerid = U.uid 
								AND E.VET < CURRENT_TIMESTAMP)
								
							UNION
							
							(SELECT 
							E.eid,
							E.image,
							E.header,
							E.date,
							E.time,
							E.location,
							E.content, 
							E.VST, 
							U.name, 
								(SELECT 
									COUNT(*)
								FROM 
									wh_friends F, 
									wh_users UU, 
									wh_event_invites EI 
								WHERE 
									  F.person1 = :owner 
									  AND F.person2 = UU.uid
									  AND F.VET IS NULL
									  AND EI.user = F.person2 
									  AND EI.event = E.eid 
									  AND EI.VET < CURRENT_TIMESTAMP) friends,
								(SELECT
									COUNT(*)
								FROM
									wh_event_invites EI
								WHERE
									EI.event = E.eid
									AND EI.VET < CURRENT_TIMESTAMP) total
							FROM 
								wh_events E, 
								wh_event_invites I, 
								wh_users U
							WHERE 
								I.user =:owner 
								AND U.uid = E.ownerid 
								AND I.event = E.eid 
								AND E.VET < CURRENT_TIMESTAMP)
							ORDER BY 
								date ASC,
								time ASC;";
					break;
				default:
					$sql = "SELECT 
							E.eid,
							E.image,
							E.header,
							E.date,
							E.time,
							E.location,
							E.content, 
							E.VST, 
							U.name, 
								(SELECT 
									COUNT(*)
								FROM 
									wh_friends F, 
									wh_users UU, 
									wh_event_invites EI 
								WHERE 
									  F.person1 = :owner 
									  AND F.person2 = UU.uid
									  AND F.VET IS NULL
									  AND EI.user = F.person2 
									  AND EI.event = E.eid 
									  AND EI.VET < CURRENT_TIMESTAMP) friends,
								(SELECT
									COUNT(*)
								FROM
									wh_event_invites EI
								WHERE
									EI.event = E.eid
									AND EI.VET < CURRENT_TIMESTAMP) total
							FROM 
								wh_events E, 
								wh_event_invites I, 
								wh_users U
							WHERE 
								I.user = :owner 
								AND U.uid = E.ownerid AND I.event = E.eid AND E.VET > CURRENT_TIMESTAMP 
							ORDER BY 
								E.date ASC,
								E.time ASC;";
					break;
			}
					
			$STH = @$this->DBH->prepare($sql);
			if($STH->execute(array('owner' => $owner)))
			{
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$events = Array();
				while($row = $STH->fetch())
				{
					$events[] = $row;
				}
				
				return $events;
			}
			else echo("Tapahtui virhe.");
		}
		
		
		public function uploadImage($file, $user, $size)
		{
			// Luodaan uusi Upload-objekti.
			$upload = Upload::factory('uploads');
			// Asetetaan objektin tiedostoksi formista saatu tiedosto.
			$upload->file($file);
			// Rajoitetaan tiedoston koko noin 200 kbit.
			$upload->set_max_file_size(1);
			// Sallitut tiedostotyypit: jpeg, gif, png.
			$upload->set_allowed_mime_types(array('image/jpeg', 'image/gif', 'image/png'));
			
			// Luodaan uusi thumbnailobjekti.
			$thumb = new easyphpthumbnail();

			// Kokeillaan suorittaa koodiblokki, heitetään Exception virheen sattuessa.
			try{
				// Uploadataan tiedosto palvelimelle ja tallennetaan infoa $results -muuttujaan.
				$results = $upload->upload();
				
				// Jos upload ei onnistunut heitetään Exception ja haetaan $results:sta ensimmäinen virheteksti.
				if(!$results["status"])
					throw new Exception($results["errors"][0]);
					
				$filePath = "./uploads/" . $results["filename"];
				$thumbPath = "./thumbs/" . $results["filename"];
				
				// Haetaan thumbnailille tiedostonimi $results:sta.
				$thumb->Thumbfilename = $results["filename"];
				// Tallennetaan thumbnail kansioon:
				$thumb->Thumblocation = "thumbs/";
				// Rajoitetaan thumbnailin leveys (px).
				$thumb->Thumbwidth = 150;
				// Luodaan thumbnail ja tallennetaan se tiedostona.
				$thumb->Createthumb('uploads/' . $results["filename"], 'file');
			
				$sql = "INSERT INTO wh_images(owner, url, thumb, VST) VALUES(:owner, :filePath, :thumbPath, CURRENT_TIMESTAMP);";
				$STH = @$this->DBH->prepare($sql);
				$STH->execute(array('owner' => $user, 'filePath' => $filePath, 'thumbPath' => $thumbPath));
				echo($filePath);
			} 
			// Jos löydetään virhe, tulostetaan virheilmoitus.
			catch(Exception $e)
			{
				echo($e->getMessage());
			}
		}
		
		
		/*
		public function uploadImage($file, $user, $size)
		{
			try
			{
				// Create new Upload-object.
				$upload = Upload::factory('uploads');
				// Assign file.
				$upload->file($file);
				// Allowed formats: jpeg, gif, png.
				$upload->set_allowed_mime_types(array('image/jpeg', 'image/gif', 'image/png'));
				// Limit file size to 0.5 Mbit
				$upload->set_max_file_size(0.5);
				
				// Create a thumbnail object.
				$thumb = new easyphpthumbnail();
				
				$size = $size/1024/1024;
				
				if($size > 0.5)
				{
					echo("Suurin sallittu tiedostokoko: 0.5 megatavua."); $_GET['id']
					exit;
				}
				
				// Upload file to server.
				$results = $upload->upload();
				if($upload->check())
				{
					//echo("moi");
					echo($results["errors"][0]);
					//var_dump($results);
					exit;
				}
				
				$filePath = "./uploads/" . $results["filename"];
				$thumbPath = "./thumbs/" . $results["filename"];
				$owner = $user;
				
				// Use same name form thumbnail.
				$thumb->Thumbfilename = $results["filename"];
				// Folder for thumbnails.
				$thumb->Thumblocation = "thumbs/";
				// Set maximum thumbnail height.
				$thumb->Thumbheight = 150;
				// Create the thumbnail and save it as a file.
				$thumb->Createthumb('uploads/' . $results["filename"], 'file');
				
			
				$sql = "INSERT INTO wh_images(owner, url, thumb, VST) VALUES(:owner, :filePath, :thumbPath, CURRENT_TIMESTAMP);";
				$STH = @$this->DBH->prepare($sql);
				$STH->execute(array('owner' => $owner, 'filePath' => $filePath, 'thumbPath' => $thumbPath));
			} catch(Exception $e)
			{
				echo($e->getMessage());
			}
		}
		*/
		
		
		public function getImages($user)
		{
			$sql = "SELECT * FROM wh_images WHERE owner = :owner ORDER BY VST DESC;";
			$STH = @$this->DBH->prepare($sql);
			if($STH->execute(array('owner' => $user)))
			{
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$images = Array();
				while($row = $STH->fetch())
				{
					$images[] = $row;
				}
				
				echo(json_encode($images));
			}
			else echo(json_encode("Tapahtui virhe."));
		}
		
		public function inviteFriend($email, $user)
		{
			if(!$this->validateEmail($email))
			{
				echo("Email ei kelpaa");
				exit;
			}
		
			$sql = "SELECT U.uid 
			FROM 
				wh_users U
			WHERE 
				U.email = :email 
				AND NOT EXISTS (SELECT * FROM wh_friends WHERE person1 = U.uid AND person2 = :user) 
				AND NOT EXISTS (SELECT * FROM wh_friend_invites WHERE person1 = :user AND person2 = U.uid)";
			/*SELECT U.uid 
			FROM 
				wh_users U 
			WHERE 
				U.email = "a@b.fi" 
				AND NOT EXISTS (SELECT * FROM wh_friends WHERE person1 = 1 AND person2 = 4) 
				AND NOT EXISTS (SELECT * FROM wh_friend_invites WHERE person1 = 4 AND person2 = U.uid)*/
			$STH = @$this->DBH->prepare($sql);
			if($STH->execute(array('email' => $email, 'user' => $user)))
			{
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$row = $STH->fetch();
				if($STH->rowCount() == 1 && $row->uid != $user)
				{
					$sql = "INSERT INTO wh_friend_invites(person1, person2, VST) VALUES(:user, :friend, CURRENT_TIMESTAMP);";
					$STH = $this->DBH->prepare($sql);
					if($STH->execute(array('user' => $user, 'friend' => $row->uid)))
					{
						echo("Kutsu lähetetty.");
					}
					else
					{
						echo("Tapahtui virhe.");
					}
				}
				else
				{
					echo("Ehkä jo kaveri?");
				}
			}
		}
		
		public function getFriends($user)
		{
			$sql = "SELECT U.name, U.email FROM wh_friends F, wh_users U WHERE F.person1 = :user AND F.person2 = U.uid";
			$STH = $this->DBH->prepare($sql);
			if($STH->execute(array('user' => $user)))
			{
				$friends = Array();
				$STH->setFetchMode(PDO::FETCH_OBJ);
				while($row = $STH->fetch())
				{
					$friends[] = $row;
				}
				return $friends;
			}
			else
			{
				echo("Tapahtui virhe");
			}
		}
		
		//GET EVENT
		public function getEvent($eventId){


			$sql = "SELECT * FROM
							wh_events 
						WHERE 
							eid = :eventId;";

				$STH = $this->DBH->prepare($sql);
				$STH->execute(array('eventId' => $eventId));


				$STH->setFetchMode(PDO::FETCH_ASSOC);
				$row = $STH->fetch();

				return($row);
		}
	}
?>