// JavaScript Document
$(document).ready(function() {
		
	$('#rs_form').validate({
		rules: {
			rs_name: {
				required: true
			},
			rs_type: {
				required: true	
			}
		}
	});
	
});