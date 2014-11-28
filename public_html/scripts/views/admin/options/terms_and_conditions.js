$(document).ready(function () {
	
	// Initialise the TinyMCE editor on the document content textarea
	var tmce = $("textarea");
	
	tmce.tinymce({
		script_url : '/scripts/tiny_mce/tiny_mce.js',
		theme : "advanced",
		plugins: "save,paste,table",
		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontsizeselect,|,forecolor,backcolor",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,table,|,undo,redo,|,hr,removeformat,cleanup",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : false
	}).css("width", "100%");
		
	$('#save').click(function () {
		
		var confirm_text
		
		if($('#on:checked').length)
		{
			confirm_text = 'Click OK to save the terms and conditions. Administrators will be required to agree to them before they can use ESCAPE IS.';
		}
		else
		{
			confirm_text = 'Click OK to disable terms and conditions. New administrators will not be required to agree to them.';
		}
		
		return confirm(confirm_text);
		
	});
	
	$("select[name=pct_id]").on("change", function() {
		$(this).parents("form").submit();
	});
		
});