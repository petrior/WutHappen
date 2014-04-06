var createAccountBtn = $('#showRegister');
var infoBtn = $('#showInfo');
var registerDiv = $('#register');
var infoDiv = $('#info');
var closeBtn = $('.close');

var eventImg = $('.eventImg');
var eventH3 = $('.eventH3');

closeBtn.click(function(event){
	event.preventDefault();
});

createAccountBtn.click(function(event){
	event.preventDefault();
	registerDiv.fadeToggle(300);
	$('body, html').animate({scrollTop: registerDiv.offset().top});
});

infoBtn.click(function(event){
	event.preventDefault();
	infoDiv.fadeToggle(300);
	$('body, html').animate({scrollTop: infoDiv.offset().top});
});

function closeDiv(dom)
{
	var a = $(dom);
	a.parent().fadeToggle(300);
}

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


// Login pressed...
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
			console.log(data);
			if(data != "1")
				$('#lError').text(data);
			else
				window.location = './wuthappen.php';
		});
}

// Register pressed...
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
				console.log(data);
				if(data != "1")
					$('#rError').text(data);
				else
					login(email, pwd);
			});
		}
	}
});

// Logout pressed...
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