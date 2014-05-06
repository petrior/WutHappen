////// LOGIN DOM ELEMENTS //////
var createAccountBtn = $('#showRegister');
var infoBtn = $('#showInfo');
var registerDiv = $('#register');
var infoDiv = $('#info');
var closeBtn = $('.close');

////// FRONT PAGE EVENT LIST DOM ELEMENTS /////
var eventImg = $('.eventImg');
var eventH3 = $('.eventH3');

////// LOGIN PAGE 'CLOSE' BUTTONS /////
closeBtn.click(function(event){
	event.preventDefault();
});

function closeDiv(dom)
{
	var a = $(dom);
	a.parent().fadeToggle(300);
}

///// LOGIN PAGE 'REGISTER FORM SHOW' BUTTON /////
createAccountBtn.click(function(event){
	event.preventDefault();
	registerDiv.fadeToggle(300);
	$('body, html').animate({scrollTop: registerDiv.offset().top});
});

///// LOGIN PAGE 'MORE INFO ELEMENT SHOW' BUTTON /////
infoBtn.click(function(event){
	event.preventDefault();
	infoDiv.fadeToggle(300);
	$('body, html').animate({scrollTop: infoDiv.offset().top});
});

///// CLICKING ELEMENT IMAGE OR HEADER WILL TOGGLE EVENT CONTENT /////
function showContent(dom)
{
	var a = $(dom);
	var img = a.parent().parent().find('.eventImage').find('.eventImg');
	var content = a.parent().parent().find('.eventHeader').find('.eventContent');
	var imgToggle;
	
	if(content.css("display") != "none") imgToggle = false;
		else imgToggle = true;
	
	$.each($('#container').children().find('.eventBar').find('.eventHeader').find('.eventContent'), function(){
		if($(this).css("display") != "none")
		{
			$(this).css("display", "none");
			$(this).parent().parent().find('.eventImage').find('.eventImg').css("max-height", "90px");
		}
	});
	
	if(imgToggle)
	{
		content.css("display", "inline");
		img.css("max-height", "100%");
	}
}


///// LOGIN PAGE 'LOGIN' BUTTON PRESSED /////
$('#loginForm').submit(function(event){
	event.preventDefault();
	
	if(!$('[name="lEmail"]').val() || !$('[name="lPwd"]').val())
	{
		$('#lError').text('Syötä sähköpostiosoite ja salasana!');
	}
	else
	{
		var email = $('[name="lEmail"]').val();
		var pwd = $('[name="lPwd"]').val();
		
		login(email, pwd);
	}
});

function login(email, pwd)
{
	$.ajax({
			url: 'server.php',
			dataType: 'json',
			data: { 'lEmail':email, 'lPwd':pwd },
			method: 'POST'
		}).done(function(data){
			if(data != "1")
				$('#lError').text(data);
			else
				window.location = './main.php';
		});
}

///// LOGIN PAGE 'REGISTER' BUTTON PRESSED /////
$('#registerForm').submit(function(event){
	event.preventDefault();
	
	if(!$('[name="rEmail"]').val() || !$('[name="rPwd1"]').val() || !$('[name="rPwd2"]').val() || !$('[name="rName"]').val())
	{
		$('#rError').text('Täytä kaikki kentät!');
	}
	else
	{
		if($('[name="rPwd1"]').val() != $('[name="rPwd2"]').val())
			$('#rError').text('Salasanat eivät täsmää!');
		else
		{
			var email = $('[name="rEmail"]').val();
			var pwd = $('[name="rPwd1"]').val();
			var name = $('[name="rName"]').val();
			
			$.ajax({
				url: 'server.php',
				dataType: 'json',
				data: { 'rEmail':email, 'rPwd':pwd, 'rName':name },
				method: 'POST'
			}).done(function(data){
				if(data != "1")
					$('#rError').text(data);
				else
					login(email, pwd);
			});
		}
	}
});

///// FRONT PAGE 'LOGOUT' PRESSED /////
$('#logout').click(function(event){
	event.preventDefault();
	
	$.ajax({
		url: 'server.php',
		data: { 'logout':'true' },
		method: 'POST'
	}).done(function(){
		window.location = './login.php';
	});
});

///// FRONT PAGE 'OWN EVENTS' PRESSED /////
$('#own').click(function(event){
	event.preventDefault();
	
	$.ajax({
		url: 'server.php',
		data: { 'eventType':'own' },
		method: 'POST',
		dataType: 'json'
	}).done(function(data){
		createList(data);
	});
});

///// FRONT PAGE 'INVITED EVENTS' PRESSED /////
$('#invited').click(function(event){
	event.preventDefault();
	
	$.ajax({
		url: 'server.php',
		data: { 'eventType':'invited' },
		method: 'POST',
		dataType: 'json'
	}).done(function(data){
		createList(data);
	});
});

///// FRONT PAGE 'PAST EVENTS' PRESSED /////
$('#past').click(function(event){
	event.preventDefault();
	
	$.ajax({
		url: 'server.php',
		data: { 'eventType':'past' },
		method: 'POST',
		dataType: 'json'
	}).done(function(data){
		createList(data);
	});
});

///// CREATE EVENTLIST FROM RETURN DATA /////
function createList(data)
{
	var events = $('#events');
	$('#eventForm').hide();
	events.show();
	events.empty();
	
	if(data == "")
	{
		var event = $('<div>');
		event.attr("class", "event");
		
		var header = $('<div>');
		header.attr("class", "eventHeader");
		
		var text = $('<p>');
		text.text("Ei tapahtumia.");
		
		header.append(text);
		event.append(header);
		events.append(event);
	} else
	{
		$.each(data, function(index){	
			var event = $('<div>');
			event.attr("class", "event");
			
			var eventBar = $('<div>');
			eventBar.attr("class", "eventBar");
			
			if(data[index].image != "false")
			{
				var eventImage = $('<div>');
				eventImage.attr("class", "eventImage");
				
				var eventImg = $('<img>');
				eventImg.attr("class", "eventImg");
				eventImg.attr("src", data[index].image);
				eventImg.attr("onclick", "showContent(this)");
			}
			
			
			
			var eventHeader = $('<div>');
			eventHeader.attr("class", "eventHeader");
			
			var eventH3 = $('<h3>');
			eventH3.attr("class", "eventH3");
			eventH3.attr("onclick", "showContent(this)");
			eventH3.text(data[index].header);
			
			var dateText = $('<p>');
			var dateParts = data[index].date.split("-");
			var date = dateParts[2] + "." + dateParts[1] + "." + dateParts[0];
			var timeParts = data[index].time.split(":");
			var time = timeParts[0] + "." + timeParts[1];
			dateText.text(date + " klo. " + time);
			
			
			
			var eventContent = $('<div>');
			eventContent.attr("class", "eventContent");
			
			var contentText = $('<p>');
			contentText.html(data[index].content.replace(/\n/gi, "<br>"));
			
			var attendance = $('<div>');
			attendance.attr("class", "attendance");
			
			var total = $('<p>');
			total.attr("class", "total");
			total.text(data[index].total);
			
			var friends = $('<p>');
			friends.attr("class", "friends");
			friends.text(data[index].friends);
			
			event.append(eventBar);
			if(data[index].image != "false")
			{
				eventBar.append(eventImage);
				eventImage.append(eventImg);
			}
			eventBar.append(eventHeader);
			eventHeader.append(eventH3);
			eventHeader.append(dateText);
			eventHeader.append(eventContent);
			eventContent.append(contentText);
			eventHeader.append(attendance);
			attendance.append(total);
			attendance.append(friends);
			
			events.append(event);
		});
	}
}

// Create new event pressed...
$('#newEvent').click(function(event){
	event.preventDefault();
	showEventForm();
});

function showEventForm()
{
	$('#events').hide();
	$('#eventForm').show();
}

$('#choosePicture').click(function(event)
{
	event.preventDefault();
	$('#picturePicker').hide();
	$('#pictureContainer').toggle();
	
	if($('#pictureContainer').css("display") != "none")
	{
		$('#pictureContainer').empty();
		
		$.ajax({
			url: 'server.php',
			dataType: 'json',
			data: { 'getImages':'true' },
			method: 'POST'
		}).done(function(data){
			$.each(data, function(index){
				var img = $('<img>');
				img.attr("src", data[index].thumb);
				if(data[index].url == $('#eventFormImg').attr("src"))
				{
					img.css("border", "2px solid blue");
				}
				img.attr("onclick", 'selectImage("' + data[index].url + '");');
				$('#pictureContainer').append(img);
			});
		});
	}
});

$('#loadPicture').click(function(event){
	event.preventDefault();
	$('#pictureContainer').hide();
	$('#picturePicker').toggle();
});

$('#uploadFileBtn').bind("click", function(event){
	event.preventDefault();
	$('.uploadInput').click();
});

$('#uploadInput').change(function(){
	$('#fileName').val($('#uploadInput').val());
});

$('#uploadImage').click(function(event){
	event.preventDefault();
	$('#uploadError').remove();

	if(!$('#uploadInput').val())
	{
		uploadError("Valitse ensin tiedosto.");
	}
	else if($('#uploadInput')[0].files[0].size/1024/1024 > 0.5)
	{
		uploadError("Maksimi tiedostokoko: 0,5 megatavua.");
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
					pXhr.upload.addEventListener('progress', progressHandler);
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
				var message = $('<p>');
				message.text(msg);
				$('#picturePicker').append(message);
			},
			error: function(){},
			data:formData,
			cache:false,
			contentType:false,
			processData:false
		});
	}
});

function progressHandler(e)
{
	if(e.lengthComputable)
	{
		$('progress').attr({value:e.loaded, max:e.total});
	}
}

function selectImage(url)
{
	$('#eventFormImg').attr("src", url);
	$('#pictureContainer').hide();
}

function uploadError(msg)
{
	if($('#uploadError').length > 0)
	{
		$('#uploadError').text(msg)
	}
	else
	{
		var message = $('<p>');
		message.text(msg);
		message.attr("id", "uploadError");
		$('#picturePicker').append(message);
	}
}

$('#eDate').datepicker({
	showOtherMonths: false,
	dayNamesMin: ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe', 'La'],
	monthNames: ['Tammikuu', 'Helmikuu', 'Maaliskuu', 'Huhtikuu', 'Toukokuu', 'Kesäkuu', 'Heinäkuu', 'Elokuu', 'Syyskuu', 'Lokakuu', 'Marraskuu', 'Joulukuu'],
	nextText: 'Seuraava',
	prevText: 'Edellinen',
	dateFormat: "dd.mm.yy",
	inline: true
});

$('#createEvent').click(function(event){
	event.preventDefault();
	
	var image;
	if($('eventFormImg').attr("src") != 'undefined' && $('eventFormImg').attr("src") != false && $('eventFormImg').attr("src") != "") image = $('#eventFormImg').attr("src");
		else image="false";
	var header = $('#eHeader').val();
	var date = $('#eDate').val();
	var time = $('#eTime').val();
	var location = $('#eLocation').val();
	var content = $('#eContent').val();
	
	if(header && date && time && location && content)
	{
		$.ajax({
				url: 'server.php',
				dataType: 'json',
				data: { 'image':image, 
						'header':header, 
						'date':date, 
						'time':time, 
						'location':location,
						'content':content },
				method: 'POST'
			}).done(function(msg){
				eventCreateError(msg);
				$('#eventFormImg').attr("src", "");
				$('#eHeader').val("");
				$('#eDate').val("");
				$('#eTime').val("");
				$('#eLocation').val("");
				$('#eContent').val("");
			});
	} else
	{
		eventCreateError("Täytä kaikki kohdat.");
	}
});

function eventCreateError(msg)
{
	if($('#eventCreateError').length > 0)
	{
		$('#eventCreateError').text(msg)
	}
	else
	{
		var message = $('<p>');
		message.text(msg);
		message.attr("id", "eventCreateError");
		$('#contentForm').append(message);
	}
}