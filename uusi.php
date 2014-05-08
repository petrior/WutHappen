<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Luo uusi tapahtuma</title>
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="main.css">
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<script>
		  $(function() {
			$( "#datepicker" ).datepicker();
		  });
		  /* Finnish initialisation for the jQuery UI date picker plugin. */
		/* Written by Harri KilpiÃ¶ (harrikilpio@gmail.com). */
		  $(function($){
			$.datepicker.regional['fi'] = {
				closeText: 'Sulje',
				prevText: '&#xAB;Edellinen',
				nextText: 'Seuraava&#xBB;',
				currentText: 'T&#xE4;n&#xE4;&#xE4;n',
				monthNames: ['Tammikuu','Helmikuu','Maaliskuu','Huhtikuu','Toukokuu','Kes&#xE4;kuu',
				'Hein&#xE4;kuu','Elokuu','Syyskuu','Lokakuu','Marraskuu','Joulukuu'],
				monthNamesShort: ['Tammi','Helmi','Maalis','Huhti','Touko','Kes&#xE4;',
				'Hein&#xE4;','Elo','Syys','Loka','Marras','Joulu'],
				dayNamesShort: ['Su','Ma','Ti','Ke','To','Pe','La'],
				dayNames: ['Sunnuntai','Maanantai','Tiistai','Keskiviikko','Torstai','Perjantai','Lauantai'],
				dayNamesMin: ['Su','Ma','Ti','Ke','To','Pe','La'],
				weekHeader: 'Vk',
				dateFormat: 'dd.mm.yy',
				firstDay: 1,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ''};
			$.datepicker.setDefaults($.datepicker.regional['fi']);
		});
		  </script>
	</head>
	<body>
		<?php
			require_once("./database/WutHappen.php");
			$wutHappen = new WutHappen();
			$wutHappen->SSLon();
			$wutHappen->startSession();
			$wutHappen->dbConnect();
			
			if($_GET['logout'] == true)
			{
				$wutHappen->endSession();
			}
			
			if(!$_SESSION['logged'])
			{
				$wutHappen->redirect("./login.php");
			}
			
			$messages = $wutHappen->getUserMessages($_SESSION['user']);
		?>
		<div class="navBar">
			<ul class="horizontalNav">
				<li class="dropdownEvent selected">
					<a href="#">Tapahtumat<img src="./images/arrowDown.png"></a>
					<ul>
						<li><a href="./main.php?eventType=own">Omat</a></li>
						<li><a href="./main.php?eventType=others">Muiden</a></li>
						<li><a href="./main.php?eventType=past">Menneet</a></li>
						<li class="selected"><a href="./uusi.php">Uusi</a></li>
					</ul>
				</li>
				<li><a href="./kaverilista.php">Kaverilista</a></li>
				<?php 
					if(count($messages) > 0)
					{
						echo("<li><a id='msgNumber' href='./ilmoitukset.php' class='navIcon'><i class='fa fa-exclamation-circle'></i>" . count($messages) . "</a></li>");
					}
				?>
				<li class="dropdownEventRight floatRight">
					<a href="#">User</a>
					<ul>
						<li><a href="<?php echo($_SERVER['PHP_SELF'] . "?logout=true"); ?>">Kirjaudu ulos</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="container">
			<div class="event"><p class="eventOwner">Tapahtumat \ Luo uusi tapahtuma</p></div>
			<div class="event">
			<input class="eventInputBtn" type='button' id='hideshow' value='Valitse kuva'>
			<div id="content" style="display: none;"></div>
			<form id="pictureForm" enctype="multipart/form-data">
						<button class="eventInputBtn" id="uploadFileBtn">Valitse tiedosto</button>
						<input class="uploadInput" type="file" name="userFile" id="uploadInput"></input>
						<input disabled="disabled" placeholder="Valitse tiedosto" type="text" class="eventInput2 fileName" id="fileName"></input>
						<button class="eventInputBtn" id="uploadImage">Lataa</button>
					</form>
			<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
				<img id="eventFormImg"></img>
				<input type="hidden" name="kuva" id="kuva"></input>
				<input id="otsikko" class="eventInput" type="text" placeholder="otsikko" size="40" name="otsikko"></input><br/>
				<input class="eventInput" type="text" id="datepicker" placeholder="päivämäärä" size="40" name="paivamaara"></input></br>
				<input id="kello" class="eventInput" type="text" placeholder="kellonaika (00.00)" size="40" name="kello"></input></br>
				<input type="text" class="eventInput" placeholder="paikka" id="paikka" size="40" name="paikka"></input></br>
				<textarea class="eventInput" id="kuvaus" cols="31" rows="5" type="text" placeholder="kuvaus" name="kuvaus"></textarea></br>
				<input type="submit" class="createButton" value="Luo tapahtuma">
				<?php
					if(isset($_POST['kuva']) && 
					isset($_POST['otsikko']) && 
					isset($_POST['paivamaara']) && 
					isset($_POST['kello']) && 
					isset($_POST['paikka']) && 
					isset($_POST['kuvaus']))
					{
						$owner = $_SESSION['user'];
						$image = $_POST['kuva'];
						$header = $_POST['otsikko'];
						$date = $_POST['paivamaara'];
						$time = $_POST['kello'];
						$location = $_POST['paikka'];
						$content = $_POST['kuvaus'];
						
						$wutHappen->dbConnect();
						$wutHappen->addEvent($owner, $image, $header, $date, $time, $location, $content);
					}
				?>
				
			</form>
			</div>
		</div>
		<script type="text/javascript" src="main.js"></script>
		<script type="text/javascript">
		//KUVADIVI
	
		$("#hideshow").click(function(){
			$('#content').toggle('show');
			$('#content').empty();
			$.ajax({
					url: 'server.php',
					dataType: 'json',
					data: { 'getImages':'true' },
					method: 'POST'
				}).done(function(data){
					$.each(data, function(index){
						var img = $('<img class="kuva">');
						img.attr("src", data[index].thumb);
						if(data[index].url == $('#eventFormImg').attr("src"))
						{
							img.css("border", "2px solid blue");
						}
						img.attr("onclick", 'selectImage("' + data[index].url + '");');
						$('#content').append(img);
					});
				});
		});
		
		//KUVAN VALINTA
		function selectImage(url){
			$('#eventFormImg').attr("src", url);
			$('#kuva').val(url);
			$('#content').toggle('show');
		}
		
		$('#uploadImage').click(function(event){
	event.preventDefault();
	console.log($('#uploadInput').val());
	//$('#uploadError').remove();

	if(!$('#uploadInput').val())
	{
		//uploadError("Valitse ensin tiedosto.");
		alert("Valitse ensin tiedosto.");
	}
	else if($('#uploadInput')[0].files[0].size/1024/1024 > 1)
	{
		//uploadError("Maksimi tiedostokoko: 0,5 megatavua.");
		alert("Maksimi tiedostokoko: 0,5 megatavua.");
	}
	else
	{
		var formData = new FormData($('#pictureForm')[0]);
		$.ajax({
			url: 'server.php',
			type: 'POST',
			xhr: function()
			{
				var pXhr = $.ajaxSettings.xhr();
				if(pXhr.upload)
				{
					//pXhr.upload.addEventListener('progress', progressHandler);
				}
				return pXhr;
			},
			beforeSend: function(){
				var progressBar = $('<progress>');
				$('#picturePicker').append(progressBar);
				$('progress').attr({value:0, max:1});
			},
			success: function(msg){
				$('progress').remove();
				console.log(msg);
				$('#eventFormImg').attr("src", msg);
				$('#kuva').val(msg);
			},
			error: function(msg){
			console.log(msg);},
			data:formData,
			cache:false,
			contentType:false,
			processData:false
		});
	}
	});
	$('#uploadFileBtn').bind("click", function(event){
	event.preventDefault();
	$('.uploadInput').click();
});
$('#uploadInput').change(function(){
	var tiedosto = $('#uploadInput').val().replace(/^.*\\/, "");
	$('#fileName').val(tiedosto);
});


</script>
	</body>
</html>