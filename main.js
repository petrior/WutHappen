$('.dropdownEvent').mouseover(function(){
	$(this).find("ul").css("left", "0px");
});

$('.dropdownEvent').mouseout(function(){
	$(this).find("ul").css("left", "-9999px");
});

$('.dropdownEventRight').mouseover(function(){
	$(this).find("ul").css({
		"left":-$(this).outerWidth() - 14 + "px"
	});
});

$('.dropdownEventRight').mouseout(function(){
	$(this).find("ul").css({
		"left":"-9999px"
	});
});

function deleteEvent(Id){
	$.ajax({
		url: 'server.php',
		data: { 'deleteEventId':Id },
		method: 'POST',
		error: function(data){
		}
	}).done(function(data){
		window.location.href=window.location.href;
	});
}

function showContent(dom)
{	
	var content = $(dom).parent().parent().find('.eventContent');
	var image = $(dom).parent().parent().find('.eventImage');
	var event = $(dom).parent().parent();
	
	if(content.css("display") == "none")
	{
		$('.eventContent').hide();
		$('.helperContainer').css("display", "block");
		$('.eventImage').css("max-height", "9em");
		
		content.parent().find('.helperContainer').css("display", "table-row");
		content.show();
		image.css("max-width", "calc(100% - 10px)");
		image.css("max-height", "200em");
		
		$('html, body').animate({
			scrollTop: $(dom).parent().parent().offset().top - 55
		}, 500);
	}
	else
	{
		content.parent().find('.helperContainer').css("display", "block");
		content.hide();
		image.css("max-height", "9em");
		
		$('html, body').animate({
			scrollTop: $(dom).parent().parent().offset().top - 55
		}, 500);
	}
}

// ILMOITUKSET
function confirmFriendInvite(dom, id, msgid)
{
	$.ajax({
		url: 'server.php',
		data: { 'confirmFriend':id, 'msgid':msgid },
		method: 'POST',
		error: function(data){
		}
	}).done(function(data){
		if(data == 1)
		{
			$(dom).parent().parent().remove();
			$('#msgNumber').text(parseInt($('#msgNumber').text()) - 1);
		}
	});
}