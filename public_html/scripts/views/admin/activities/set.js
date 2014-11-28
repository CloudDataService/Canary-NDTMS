// JavaScript Document
$(document).ready(function () {
		
	$('#support_group_form').validate({
		rules: {
			sp_name: {
				required: true
			},
			sp_description: {
				required: true
			}
		}
	});
	
});