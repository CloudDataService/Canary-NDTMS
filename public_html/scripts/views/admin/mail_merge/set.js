// JavaScript Document
$(document).ready(function() {
	
	// validate mail merge form
	$('#mail_merge_form').validate({
		rules: {
			mm_title: {
				required: true
			}
		}
	});	
		
	$('textarea').tinymce({
		// Location of TinyMCE script
		script_url : '/scripts/tiny_mce/tiny_mce.js',

		// General options
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,insertimage,|,insertdate,inserttime,|,forecolor,backcolor",
		theme_advanced_buttons3 : "hr,removeformat,|,emotions,iespell,media,advhr",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		//content_css : "/css/screen.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",
	});

	// if preview button clicked
	$('#preview').click(function () {

		$.post('/admin/mail-merge/preview', $('#mail_merge_form').serialize(), function(data) {
			
			// get div
			var preview_div = $("#preview_div");
			
			// load content
			preview_div.html(data);
			
			// open dialog
			preview_div.dialog({width: 650,
				resizable: false,
				modal: true,
				zIndex: 1
			});
			
		});
		
		// prevent default action of link
		return false;
	});
	
	// when clicking on a tag
	$('td.tags span').click(function() {

		// get actual tag text
		var tag = "<span>" + $(this).text() + "</span>";
		
		// append tag to textarea
		//$('#mm_body').append(tag);
		tinyMCE.execCommand('mceInsertContent', false, tag );
	});
			
});