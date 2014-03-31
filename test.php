<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<?php
			// Tämä tarvitaan tietenkin sivulle...
			require_once("./database/WutHappen.php");
			
			// Eka asia koodissa.
			$wutHappen = new WutHappen();
			
			// Pakotetaan https.
			$wutHappen->SSLon();
			
			// Turha testi.
			var_dump($wutHappen->getConnectionInfo());
			
			// Luo database handlerin. Tarvitaan jos haluaa suorittaa MySQL-komentoja.
			$wutHappen->dbConnect();
			
			// Luodaan sessio.
			$wutHappen->startSession();
			
			// Login funktio on helppo käyttää (sähköposti, salasana).
			$wutHappen->login("c@d.fi", "Salasana5");
			
			// Lopetetaan sessio.
			$wutHappen->endSession();
			
			// Rekisteröidään uusi käyttäjä (sähköposti, salasana).
			$wutHappen->register("c@d.fi", "Salasana5");
		?>
	</body>
</html>