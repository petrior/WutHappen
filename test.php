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
			
			// Luo database handlerin. Tarvitaan jos haluaa suorittaa MySQL-komentoja.
			$wutHappen->dbConnect();
			
			// Luodaan sessio.
			$wutHappen->startSession();
			
			// Login funktio on helppo käyttää (sähköposti, salasana).
			//$wutHappen->login("b@c.fi", "Salasana5");
			
			// Lopetetaan sessio.
			$wutHappen->endSession();
			
			// Rekisteröidään uusi käyttäjä (sähköposti, salasana, nimi).
			//$wutHappen->register("b@c.fi", "Salasana5", 'Matti-Näsä');
			
			// Luodaan uusi tapahtuma (käyttäjän id, kuvan id, sisältö teksti, päivämäärä)
			//$wutHappen->addEvent(1, 1, "Testitapahtuma", "2014-04-20 12:00:00");
			
			// Muokataan tapahtumaa (käyttäjän id, kuvan id, sisältö teksti, päivämäärä, vanhan tapahtuman id)
			//$wutHappen->updateEvent(1, 1, "Testitapahtuma muokattu", "2015-04-10 08:30:00", 3);
			
			// Poistetaan tapahtuma (käyttäjän id, tapahtuman id)
			//$wutHappen->removeEvent(1, 4);
			
			// Parametri "vierailijoiden" kutsumiseen.
			//echo($wutHappen->generateGuestParameter());
			
			// Profiilin päivitys (käyttäjän id, salasana, nimi, osoite, avatar id)
			//$wutHappen->updateProfile(9, "Salasana5", "Urho Kekkonen", "katu 6", 1);
		?>
	</body>
</html>