// JavaScript Document
$(document).ready(function () {
		
	// bind dialog box to appointment and event div
	$( "#risk_type" ).dialog({
		width: 450,
		resizable: false,
		modal: true,
		autoOpen: false,
		zIndex: 1
	});
	
	// if "add event" button clicked
	$('#risk_type_btn').click(function () {
		$( "#risk_type" ).dialog('open');
		return false;
	});

	if($('#cr_rt_id').length)
	{
		$('#risk_type').dialog('open');
	}
		
	$('#risk_type_form').validate({
		rules: {
			rt_id: {
				required: true
			}
		}
	});
	
	// Enable autosave
	autosave.init("#risk_summaries_form");
	
	
});