$(function(){
	// show messages
	$('#error_message').fadeIn("slow");
	$('#info_message').fadeIn("slow");

	// zebra tables
	$("tr:nth-child(even)").addClass("even");

	// menu
	$('#navigation li:last').addClass('last');
    
    $("#dialog").dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		buttons: {
			'Cancel': function() {
				$(this).dialog('close');
			},
			'Delete': function() {
				window.location = $("#dialog").dialog('option', 'href');
				$(this).dialog('close');
			}
		}
	});
    
    // confirm before delete
	$('a.confirm').click(function(){
		$("#dialog").dialog('option', 'href', this.href);
		$("#dialog").dialog('open');
		
		return false;
	});
	
});


// Upload-Extension

$(function(){
	$("#uploader").dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		title: "Upload image",
		buttons: {
			'Cancel': function() {
				$(this).dialog('close');
			},
			'Upload': function() {
				$("#imageuploadform").submit();
			}
		}
	});
});

function stopUploadError(errormsg)
{	
	if(errormsg=="size") errormsg="Das Bild ist zu groß";
	if(errormsg=="type") errormsg="Kein gültiges Bild";
	$('#uploadmessage').text(errormsg);
}

function stopUploadSuccess(filename)
{
	$('#uploader').dialog('close');
	ed = $('#uploader').dialog('option', 'insertin');
	ed.selection.setContent('<img src="' + filename + '" />');
}
